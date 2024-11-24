<?php

namespace App\Services;

use App\Models\News;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Activitylog\Models\Activity;

/**
 * سرویس مدیریت اخبار
 *
 * این کلاس وظیفه مدیریت و پردازش عملیات CRUD بر روی اخبار
 * را بر عهده دارد.
 */
class NewsService
{
    /**
     * دریافت تمامی اخبار
     *
     * این متد تمامی اخبار را با توجه به پارامترهای صفحه‌بندی
     * دریافت می‌کند.
     *
     * @param int $perPage تعداد اخبار در هر صفحه
     * @param int|null $page شماره صفحه
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $perPage,
        int $page,
    ) {
        $news = News::with('category')
            ->select('id', 'title', 'title_second', 'summary', 'body', 'image', 'tags', 'category_id', 'created_by')
            ->where('status', 'published')
            ->paginate($perPage, ['*'], 'page', $page);

        return $news;
    }

    /**
     * دریافت یک خبر با شناسه مشخص
     *
     * این متد خبر با شناسه مشخص را از پایگاه داده دریافت می‌کند.
     *
     * @param int $newsId شناسه خبر
     * @return News
     */
    public function getById(int $id)
    {
        $news = News::select('id', 'title', 'title_second', 'summary', 'body', 'image', 'tags', 'category_id', 'created_by')
            ->where('status', 'published')
            ->findOrFail($id);

        return $news;
    }

    /**
     * ذخیره‌سازی یک خبر جدید.
     *
     * این متد اطلاعات خبر را دریافت کرده و آن را در پایگاه داده ذخیره می‌کند.
     *
     * @param string|null $title عنوان خبر.
     * @param int|null $categoryId شناسه دسته‌بندی خبر.
     * @param string|null $titleSecond زیرعنوان خبر.
     * @param string|null $slug نام یکتای خبر برای استفاده در URL.
     * @param string|null $summary خلاصه خبر.
     * @param string|null $body متن اصلی خبر.
     * @param string|null $image تصویر خبر (اختیاری).
     * @param string|null $tags برچسب‌های خبر.
     * @param string|null $status وضعیت خبر (اختیاری).
     * @param int $createdBy شناسه کاربری که خبر را ایجاد کرده است.
     *
     * @return News خبر جدید ایجاد شده.
     *
     * @throws Exception در صورت بروز خطا در ذخیره‌سازی.
     */
    public function store(
        ?string $title,
        ?int $categoryId,
        ?string $titleSecond,
        ?string $slug,
        ?string $summary,
        ?string $body,
        ?string $image = null,
        ?string $tags,
        ?string $status = null,
        int $createdBy,
    ): News {
        if (request()->hasFile('image')) {
            $file = request()->file('image');
            $extension = $file->getClientOriginalExtension(); // پسوند فایل
            $newFileName = 'image_' . time() . '_' . uniqid() . '.' . $extension; // نام جدید
            $file->move(public_path('uploads'), $newFileName); // ذخیره با نام جدید
            $image = $newFileName; // نام جدید تصویر
        } else {
            $image = null; // در صورت عدم وجود تصویر
        }

        $data = [
            'title' => $title,
            'category_id' => $categoryId,
            'title_second' => $titleSecond,
            'slug' => $slug,
            'summary' => $summary,
            'body' => $body,
            'image' => $image,
            'tags' => $tags,
            'status' => $status,
            'created_by' => $createdBy,
        ];

        return News::create(array_filter($data));
    }

    /**
     * به‌روزرسانی اطلاعات یک خبر موجود.
     *
     * این متد اطلاعات خبر را بر اساس شناسه آن به‌روزرسانی می‌کند.
     *
     * @param int $newsId شناسه خبر که باید به‌روزرسانی شود.
     * @param string|null $title عنوان جدید خبر.
     * @param int|null $categoryId شناسه دسته‌بندی جدید خبر.
     * @param string|null $titleSecond زیرعنوان جدید خبر.
     * @param string|null $slug نام یکتای جدید خبر برای استفاده در URL.
     * @param string|null $summary خلاصه جدید خبر.
     * @param string|null $body متن اصلی جدید خبر.
     * @param string|null $image تصویر جدید خبر (اختیاری).
     * @param string|null $tags برچسب‌های جدید خبر.
     * @param string|null $status وضعیت جدید خبر.
     * @param int $updatedBy شناسه کاربری که خبر را به‌روزرسانی کرده است.
     *
     * @return News خبر به‌روزرسانی شده.
     *
     * @throws ModelNotFoundException در صورت عدم پیدا کردن خبر با شناسه مشخص شده.
     */
    public function update(
        int $id,
        ?string $title,
        ?int $categoryId,
        ?string $titleSecond,
        ?string $slug,
        ?string $summary,
        ?string $body,
        ?string $image,
        ?string $tags,
        ?string $status,
        int $updatedBy,
    ) {
        $news = News::findOrFail($id);

        if (request()->hasFile('image')) {
            $file = request()->file('image');
            $extension = $file->getClientOriginalExtension(); // پسوند فایل
            $newFileName = 'image_' . time() . '_' . uniqid() . '.' . $extension; // نام جدید

            // ذخیره فایل در public/uploads
            $file->move(public_path('uploads'), $newFileName);
            $image = $newFileName; // به‌روزرسانی تصویر
        }

        $data = [
            'title' => $title,
            'category_id' => $categoryId,
            'title_second' => $titleSecond,
            'slug' => $slug,
            'summary' => $summary,
            'body' => $body,
            'image' => $image,
            'tags' => $tags,
            'status' => $status,
            'updated_by' => $updatedBy,
        ];

        $news->update(array_filter($data));

        return $news->fresh();
    }

    /**
     * حذف یک خبر با شناسه مشخص
     *
     * این متد خبر با شناسه مشخص را از پایگاه داده حذف می‌کند.
     * تنها کاربران با نقش 'admin' مجاز به حذف اخبار هستند.
     *
     * @param int $newsId شناسه خبر
     * @return bool
     * @throws Exception در صورت عدم مجوز کاربر
     */
    public function delete(int $id)
    {
        $news = $this->getById($id);

        if (!$news) {
            throw new ModelNotFoundException('news_not_found');
        }

        return $news->update(['status' => 'trashed']);
    }

    /**
     * بازیابی یک خبر حذف‌شده
     *
     * این متد خبر با شناسه مشخص را بازیابی می‌کند.
     * تنها کاربران با نقش 'admin' مجاز به بازیابی اخبار هستند.
     *
     * @param int $newsId شناسه خبر
     * @throws Exception در صورت عدم مجوز کاربر
     */
    public function restore($id)
    {
        $news = $this->getById($id);

        if (!$news) {
            throw new ModelNotFoundException('news_not_found');
        }

        $news->update(['status' => 'published']);

        return $news->fresh();
    }

    /**
     * بازگشت به نسخه‌ای قبلی از خبر.
     *
     * @param int $newsId شناسه خبر
     * @param int $revisionId شناسه فعالیت
     * @return News
     * @throws Exception در صورت عدم وجود خبر یا خطا
     */
    public function revertToRevision(int $id, int $revisionId): News
    {
        $news = News::findOrFail($id);

        // پیدا کردن فعالیت مرتبط
        $activity = Activity::find($revisionId);

        if (!$activity || $activity->subject_id !== $id) {
            throw new Exception('revision_not_found');
        }

        try {
            $oldAttributes = json_decode($activity->properties, true)['attributes'];
            $news->fill($oldAttributes);
            $news->save();

            return $news; // خبر به‌روزرسانی‌ شده
        } catch (Exception $e) {
            throw new Exception('revert_failed');
        }
    }
}
