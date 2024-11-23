<?php

namespace App\DTOs\User;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس UpdateUserDTO برای مدیریت اطلاعات به‌روزرسانی کاربر.
 * 
 * این کلاس شامل فیلدهای لازم برای به‌روزرسانی اطلاعات کاربر است.
 */
class UpdateUserDTO
{
    /**
     * @var int شناسه کاربر که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['nullable', 'integer', 'unique:users', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $userId;

    /**
     * @var string|null نام کاربری جدید کاربر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:users', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $userName;

    /**
     * @var string|null نام کوچک جدید کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $firstName;

    /**
     * @var string|null نام خانوادگی جدید کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $lastName;

    /**
     * @var string|null ایمیل جدید کاربر که باید معتبر و حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:users', 'email', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $email;

    /**
     * @var string|null رمز عبور جدید کاربر که حداقل باید 8 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'min:8'])]
    #[Cast(StringCast::class)]
    public ?string $password;

    /**
     * سازنده کلاس UpdateUserDTO.
     *
     * @param int $userId شناسه کاربر.
     * @param string|null $userName نام کاربری جدید.
     * @param string|null $firstName نام کوچک جدید.
     * @param string|null $lastName نام خانوادگی جدید.
     * @param string|null $email ایمیل جدید.
     * @param string|null $password رمز عبور جدید.
     */
    public function __construct(
        int $userId,
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password
    ) {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }
}
