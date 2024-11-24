<?php

namespace App\DTOs\User;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\StringCast;


/**
 * کلاس LoginDTO برای مدیریت اطلاعات ورود کاربر.
 * 
 * این کلاس شامل فیلدهای ایمیل و رمز عبور است که برای احراز هویت کاربر استفاده می‌شود.
 */
class LoginDTO
{
    /**
     * @var string|null ایمیل کاربر که باید معتبر و فرمت درست داشته باشد.
     */
    #[Rules(['required', 'string', 'email'])]
    #[Cast(StringCast::class)]
    public ?string $email;

    /**
     * @var string|null رمز عبور کاربر که حداقل باید شامل 8 کاراکتر باشد.
     */
    #[Rules(['required', 'string', 'min:8'])]
    #[Cast(StringCast::class)]
    public ?string $password;

    /**
     * سازنده کلاس LoginDTO.
     *
     * @param string|null $email ایمیل کاربر.
     * @param string|null $password رمز عبور کاربر.
     */
    public function __construct(
        ?string $email,
        ?string $password,
    ) {
        $this->email = $email;
        $this->password = $password;
    }
}
