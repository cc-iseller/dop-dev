<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\MidtransService;

class SubscriptionCheckoutPage extends Component
{
    public ?Store $store = null;
    public ?Plan $currentPlan = null;

    public bool $isPro = false;

    // UI fallback (kalau price di DB masih 0)
    public int $proPrice = 35000;

    // dipakai oleh JS snap handler
    public ?string $snapToken = null;

    public function mount(): void
    {
        if (! auth()->check()) {
            return;
        }

        $storeId = auth()->user()->currentStoreId();
        if (! $storeId) {
            Notification::make()->title('Store belum dipilih')->danger()->send();
            return;
        }

        $this->store = Store::find($storeId);

        $sub = Subscription::with('plan')->where('store_id', $storeId)->first();
        $this->currentPlan = $sub?->plan;
        $this->isPro = ($this->currentPlan?->code === 'pro');

        $dbPrice = (int) (Plan::where('code', 'pro')->value('price') ?? 0);
        $this->proPrice = $dbPrice > 0 ? $dbPrice : 35000;
    }

    public function upgradeToPro(MidtransService $midtrans): void
    {
        if (! $this->store) {
            Notification::make()->title('Store belum dipilih')->danger()->send();
            return;
        }

        if ($this->isPro) {
            Notification::make()->title('Toko sudah Pro')->success()->send();
            return;
        }

        $proPlan = Plan::where('code', 'pro')->first();
        if (! $proPlan) {
            Notification::make()->title('Plan Pro tidak ditemukan')->danger()->send();
            return;
        }

        $amount = (int) $proPlan->price;
        if ($amount <= 0) {
            $amount = (int) $this->proPrice; // fallback kalau DB masih 0
        }

        try {
            [$snapToken, $paymentId] = DB::transaction(function () use ($midtrans, $proPlan, $amount) {

                // pastikan subscription ada
                $subscription = Subscription::firstOrCreate(
                    ['store_id' => $this->store->id],
                    [
                        // sementara “kunci” ke pro, tapi status belum aktif sampai webhook sukses
                        'plan_id' => $proPlan->id,
                        'status' => 'inactive',
                        'started_at' => now(),
                    ]
                );

                // kalau subscription sudah ada tapi plan masih free → set ke pro tapi inactive
                if ($subscription->plan_id !== $proPlan->id) {
                    $subscription->update([
                        'plan_id' => $proPlan->id,
                        'status' => 'inactive',
                    ]);
                }

                $orderId = 'SUBS-' . now()->format('Ymd-His') . '-' . random_int(100, 999);

                $payment = SubscriptionPayment::create([
                    'subscription_id' => $subscription->id,
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'status' => 'pending',
                    'payload' => null,
                ]);

                // ✅ FIX UTAMA: kirim OBJECT $payment, bukan array
                $snapToken = $midtrans->createSubscriptionSnapToken($payment);

                // simpan snap token biar gampang debug
                $payment->update([
                    'payload' => [
                        'snap_token' => $snapToken,
                        'plan' => $proPlan->code,
                        'store_id' => $this->store->id,
                        'subscription_id' => $subscription->id,
                        'payment_id' => $payment->id,
                    ],
                ]);

                return [$snapToken, $payment->id];
            });

            // set property untuk JS handler
            $this->snapToken = $snapToken;

            $this->dispatch('open-midtrans', token: $snapToken);

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal membuat pembayaran')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.subscription-checkout-page');
    }
}
