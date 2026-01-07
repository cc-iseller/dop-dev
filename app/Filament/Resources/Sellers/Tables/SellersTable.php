<?php

namespace App\Filament\Resources\Sellers\Tables;

use App\Models\Store;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class SellersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Store')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.email')
                    ->label('Email Owner')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('subscription.plan.code')
                    ->label('Paket')
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                    ->colors([
                        'success' => 'pro',
                        'gray' => 'free',
                    ])
                    ->placeholder('-'),

                BadgeColumn::make('subscription.status')
                    ->label('Status Subscription')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'expired',
                        'danger' => 'canceled',
                        'gray' => 'pending',
                    ])
                    ->placeholder('-'),

                BadgeColumn::make('subscription.latestPayment.status')
                    ->label('Status Pembayaran Terakhir')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'gray' => 'expired',
                    ])
                    ->placeholder('Belum ada'),

                TextColumn::make('subscription.latestPayment.order_id')
                    ->label('Order ID')
                    ->copyable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subscription.latestPayment.amount')
                    ->label('Amount')
                    ->money('IDR', true)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subscription.latestPayment.created_at')
                    ->label('Last Payment')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->label('Paket')
                    ->options([
                        'free' => 'FREE',
                        'pro' => 'PRO',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) return $query;

                        return $query->whereHas(
                            'subscription.plan',
                            fn (Builder $q) => $q->where('code', $value)
                        );
                    }),

                Tables\Filters\SelectFilter::make('subscription_status')
                    ->label('Status Subscription')
                    ->options([
                        'active' => 'active',
                        'expired' => 'expired',
                        'canceled' => 'canceled',
                        'pending' => 'pending',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) return $query;

                        return $query->whereHas(
                            'subscription',
                            fn (Builder $q) => $q->where('status', $value)
                        );
                    }),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'paid',
                        'pending' => 'pending',
                        'failed' => 'failed',
                        'expired' => 'expired',
                        'canceled' => 'canceled',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) return $query;

                        return $query->whereHas(
                            'subscription.latestPayment',
                            fn (Builder $q) => $q->where('status', $value)
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
