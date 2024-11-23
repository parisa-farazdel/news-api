<?php

namespace App\DTOs\News;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس CreateNewsDTO برای مدیریت اطلاعات ایجاد خبر.
 * 
 * این کلاس شامل فیلدهای لازم برای ایجاد خبر است.
 */
class CreateNewsDTO
{
    /**
     * @var string عنوان خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['string', 'unique:news', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $title;

    /**
     * @var int شناسه شاخه خبری که اطلاعات آن باید ایجاد شود.
     */
    #[Rules(['integer', 'unique:news', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $categoryId;

    /**
     * @var string روزتیتر خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $titleSecond;

    /**
     * @var string slug خبر برای استفاده در url.
     */
    #[Rules(['string', 'unique:news', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $slug;

    /**
     * @var string خلاصه متن خبری که اطلاعات آن باید ایجاد شود.
     */
    #[Rules(['string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $summary;

    /**
     * @var string متن اصلی خبری که اطلاعات آن باید ایجاد شود.
     */
    #[Rules(['string'])]
    #[Cast(StringCast::class)]
    public ?string $body;

    /**
     * @var mixed فایل تصویر خبری که باید ایجاد شود.
     */
    #[Rules(['file', 'mimes:jpg,png,pdf', 'max:2048'])]
    #[Cast(StringCast::class)]
    public ?string $image;

    /**
     * @var string برچسب‌های خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $tags;

    /**
     * @var string وضعیت خبر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $status;

    /**
     * سازنده کلاس CreateNewsDTO.
     *
     * @param string|null $title عنوان.
     * @param string|null $categoryId شناسه شاخه.
     * @param string|null $titleSecond روزتیتر.
     * @param string|null $slug رشته یکتای url.
     * @param string|null $summary متن کوتاه.
     * @param string|null $body متن اصلی.
     * @param string|null $image تصویر.
     * @param string|null $tags تگ‌ها.
     * @param string|null $status وضعیت.
     */
    public function __construct(
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
