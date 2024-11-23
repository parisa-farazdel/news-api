<?php

namespace App\DTOs\User;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس CreateUserDTO برای مدیریت اطلاعات کاربر جدید.
 * 
 * این کلاس شامل فیلدهای لازم برای ایجاد یک کاربر جدید است.
 */
class CreateUserDTO
{
    /**
     * @var string|null نام کاربری کاربر که باید منحصر به فرد و حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['required', 'string', 'unique:users', 'max:255'])]
    #[Cast(StringCast::class)]
    public $userName;

    /**
     * @var string|null نام کوچک کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['required', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public $firstName;

    /**
     * @var string|null نام خانوادگی کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['required', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public $lastName;

    /**
     * @var string|null ایمیل کاربر که باید منحصر به فرد، معتبر و حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['required', 'string', 'unique:users', 'email', 'max:255'])]
    #[Cast(StringCast::class)]
    public $email;

    /**
     * @var string|null رمز عبور کاربر که حداقل باید 8 کاراکتر و شامل حداقل یک حرف بزرگ، یک حرف کوچک و یک عدد باشد.
     */
    #[Rules(['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/'])]
    #[Cast(StringCast::class)]
    public $password;

    /**
     * سازنده کلاس CreateUserDTO.
     *
     * @param string|null $userName نام کاربری کاربر.
     * @param string|null $firstName نام کوچک کاربر.
     * @param string|null $lastName نام خانوادگی کاربر.
     * @param string|null $email ایمیل کاربر.
     * @param string|null $password رمز عبور کاربر.
     */
    public function __construct(
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password
    ) {
        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }
}
