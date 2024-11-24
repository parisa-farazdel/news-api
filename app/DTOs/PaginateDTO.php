<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;

/**
 * کلاس PaginateDTO برای مدیریت اطلاعات صفحه‌بندی است.
 * 
 * این کلاس شامل فیلدهای لازم برای صفحه‌بندی اطلاعات است.
 */
class PaginateDTO
{
    /**
     * @var int تعداد نتایج در هر صفحه که باید حداقل 1 و حداکثر 100 باشد.
     */
    #[Rules(['nullable', 'integer', 'min:1', 'max:100'])]
    #[Cast(IntegerCast::class)]
    public ?int $perPage;

    /**
     * @var int شماره صفحه برای صفحه‌بندی که باید حداکثر 255 باشد.
     */
    #[Rules(['nullable', 'integer'])]
    #[Cast(IntegerCast::class)]
    public ?int $page;

    /**
     * سازنده کلاس PaginateDTO.
     *
     * @param int $page شماره صفحه.
     * @param int $perPage تعداد نتایج در هر صفحه.
     */
    public function __construct(
        ?int $perPage = 10,
        ?int $page = 1,
    ) {
        $this->perPage = $perPage ?? 10;
        $this->page = $page ?? 1;
    }
}
