<?php

namespace App\DTOs\News;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس UpdateNewsDTO برای مدیریت اطلاعات به‌روزرسانی خبر.
 * 
 * این کلاس شامل فیلدهای لازم برای به‌روزرسانی اطلاعات خبر است.
 */
class UpdateNewsDTO
{
    /**
     * @var int شناسه خبر که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['integer', 'unique:news', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $newsId;

    /**
     * @var string|null عنوان جدید خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:news', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $title;

    /**
     * @var int شناسه شاخه جدید خبر که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['nullable', 'integer', 'unique:news', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $categoryId;

    /**
     * @var string|null روزتیتر جدید خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $titleSecond;

    /**
     * @var string|null slug جدید خبر برای استفاده در url.
     */
    #[Rules(['nullable', 'string', 'unique:news', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $slug;

    /**
     * @var string|null خلاصه متن خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $summary;

    /**
     * @var string|null متن اصلی جدید خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string'])]
    #[Cast(StringCast::class)]
    public ?string $body;

    /**
     * @var string|null تصویر جدید خبر که اطلاعات آن باید به‌روزرسانی باشد.
     */
    #[Rules(['nullable', 'file', 'mimes:jpg,png,pdf', 'max:2048'])]
    #[Cast(StringCast::class)]
    public ?string $image;

    /**
     * @var string|null برچسب‌های جدید خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $tags;

    /**
     * @var string|null وضعیت جدید خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $status;

    /**
     * سازنده کلاس UpdateNewsDTO.
     *
     * @param int $newsId شناسه خبر.
     * @param string|null $title عنوان جدید.
     * @param string|null $categoryId شناسه شاخه جدید.
     * @param string|null $titleSecond روزتیتر جدید.
     * @param string|null $slug رشته یکتای url جدید.
     * @param string|null $summary متن کوتاه جدید.
     * @param string|null $body متن اصلی جدید.
     * @param string|null $image تصویر جدید.
     * @param string|null $tags تگ‌های جدید جدید.
     * @param string|null $status وضعیت جدید.
     */
    public function __construct(
        int $newsId,
        ?string $title,
        ?int $categoryId,
        ?string $titleSecond,
        ?string $slug,
        ?string $summary,
        ?string $body,
        ?string $image,
        ?string $tags,
        ?string $status,
    ) {
        $this->newsId = $newsId;
        $this->title = $title;
        $this->categoryId = $categoryId;
        $this->titleSecond = $titleSecond;
        $this->slug = $slug;
        $this->summary = $summary;
        $this->body = $body;
        $this->image = $image;
        $this->tags = $tags;
        $this->status = $status;
    }
}
