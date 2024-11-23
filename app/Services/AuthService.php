<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس AuthService
 * 
 * این کلاس مسئول انجام عملیات احراز هویت کاربران شامل 
 * لاگین، لاگ‌اوت و به‌روزرسانی اطلاعات کاربر می‌باشد.
 */
class AuthService
{
    /**
     * ورود کاربر با استفاده از ایمیل و رمز عبور.
     *
     * @param string $email ایمیل کاربر
     * @param string $password رمز عبور کاربر
     * @return string توکن دسترسی کاربر
     * @throws Exception در صورت عدم موفقیت در ورود
     */
    public function login(
        string $email,
        string $password,
    ) {
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($data)) {
            $user = Auth::user();

            if ($user->status !== 'published') {
                $this->logout($user->id); // خروج از سیستم در صورت عدم تطابق

                throw new Exception('user_not_published');
            }

            // ایجاد توکن برای کاربر
            $token = $user->createToken('access_token')->plainTextToken;

            return $token;
        }

        throw new Exception('unauthorized_to_login'); // در صورت عدم موفقیت
    }

    /**
     * خروج کاربر با استفاده از شناسه کاربر.
     *
     * @param $userId شناسه کاربر
     * @return bool نتیجه عملیات خروج
     * @throws Exception در صورت عدم موفقیت در خروج
     */
    public function logout($userId)
    {
        $user = Auth::user();

        if ($user && $user->id === $userId) {
            $user->tokens()->delete();
            Auth::logout();

            return true;
        }

        throw new Exception('unauthorized_to_logout'); // در صورت عدم موفقیت
    }
}
