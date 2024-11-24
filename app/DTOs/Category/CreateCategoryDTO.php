<?php

namespace App\DTOs\Category;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس CreateCategoryDTO برای مدیریت اطلاعات دسته‌بندی جدید.
 * 
 * این کلاس شامل فیلدهای لازم برای اطلاعات دسته‌بندی جدید است.
 */
class CreateCategoryDTO
{
    /**
     * @var string|null عنوان دسته‌بندی جدید که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:news', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $title;

    /**
     * @var int شناسه والد دسته‌بندی جدید که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['nullable', 'integer', 'unique:news', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $parentId;

    /**
     * @var string|null وضعیت دسته‌بندی جدید که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $status;

    /**
     * سازنده کلاس CreateCategoryDTO.
     * 
     * @param string|null $title عنوان .
     * @param string|null $parentId شناسه دسته‌بندی والد .
     * @param string|null $status وضعیت .
     */
    public function __construct(
        ?string $title,
        ?int $parentId,
        ?string $status,
    ) {
        $this->title = $title;
        $this->parentId = $parentId;
        $this->status = $status;
    }
}
