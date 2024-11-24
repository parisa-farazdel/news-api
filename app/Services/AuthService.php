<?php

namespace App\Services;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * کلاس AuthService
 * 
 * این کلاس مسئول انجام عملیات احراز هویت کاربران شامل 
 * لاگین، لاگ‌اوت و به‌روزرسانی اطلاعات کاربر می‌باشد.
 */
class AuthService
{
    /**
     * ورود به سیستم با استفاده از ایمیل و رمز عبور.
     *
     * این متد تلاش می‌کند تا کاربر را با استفاده از ایمیل و رمز عبور وارد کند.
     * در صورتی که ورود موفقیت‌آمیز باشد، وضعیت کاربر بررسی می‌شود. 
     * اگر وضعیت کاربر 'published' نباشد، کاربر از سیستم خارج می‌شود و 
     * استثنای AuthorizationException پرتاب می‌شود.
     *
     * @param string $email ایمیل کاربر
     * @param string $password رمز عبور کاربر
     * @return string توکن دسترسی کاربر
     * @throws AuthorizationException اگر وضعیت کاربر 'published' نباشد.
     * @throws ValidationException اگر اطلاعات ورود نامعتبر باشد.
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

                throw new AuthorizationException('user_is_not_published');
            }

            // ایجاد توکن برای کاربر
            $token = $user->createToken('access_token')->plainTextToken;

            return $token;
        }

        // در صورت عدم موفقیت
        throw new ValidationException('Unauthorized');
    }


    /**
     * خروج از سیستم برای کاربر مشخص.
     *
     * این متد کاربر جاری را بررسی می‌کند و در صورتی که ID کاربر جاری با 
     * ID کاربر مورد نظر مطابقت داشته باشد، توکن‌های او حذف شده و از سیستم 
     * خارج می‌شود. در غیر این صورت، استثنای AuthenticationException پرتاب می‌شود.
     *
     * @param int $userId ID کاربر که باید از سیستم خارج شود.
     * @return bool در صورت موفقیت، true برمی‌گرداند.
     * @throws AuthenticationException اگر کاربر جاری مجاز به خروج نباشد.
     */
    public function logout($userId)
    {
        $user = Auth::user();

        if ($user && $user->id === $userId) {
            $user->tokens()->delete();
            Auth::logout();

            return true;
        }

        throw new AuthenticationException('unauthorized'); // در صورت عدم موفقیت
    }
}
