<?php

namespace App\DTOs\Category;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس UpdateCategoryDTO برای مدیریت اطلاعات به‌روزرسانی دسته‌بندی.
 * 
 * این کلاس شامل فیلدهای لازم برای به‌روزرسانی اطلاعات دسته‌بندی است.
 */
class UpdateCategoryDTO
{
    /**
     * @var int شناسه دسته‌بندی که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['integer', 'unique:news', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $categoryId;

    /**
     * @var string|null عنوان جدید دسته‌بندی که باید حداکثر 255 کاراکتر باشد.
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
     * @var string|null وضعیت جدید دسته‌بندی که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $status;

    /**
     * سازنده کلاس UpdateNewsDTO.
     *
     * @param int $categoryId شناسه دسته‌بندی.
     * @param string|null $title عنوان جدید.
     * @param string|null $parentId شناسه دسته‌بندی والد جدید.
     * @param string|null $status وضعیت جدید.
     */
    public function __construct(
        int $categoryId,
        ?string $title,
        ?int $parentId,
        ?string $status,
    ) {
        $this->categoryId = $categoryId;
        $this->title = $title;
        $this->parentId = $parentId;
        $this->status = $status;
    }
}
