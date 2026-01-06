<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    public function redirect()
    {
        // Defaultnya pakai state (lebih aman). Kalau di environment kamu session sering bermasalah,
        // baru ganti ke ->stateless() di sini & callback.
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Cari user berdasarkan email, kalau belum ada buat baru
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'User',
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                // pastikan kolom password nullable, atau isi random:
                'password' => bcrypt(Str::random(32)),
            ]
        );

        Auth::login($user, remember: true);

        // Kalau sebelumnya user “ditahan” oleh middleware auth, dia bakal balik ke halaman itu.
        // Kalau tidak, fallback ke /dashboard (ubah sesuai kebutuhanmu).
        return redirect()->intended('/dashboard');
    }
}
