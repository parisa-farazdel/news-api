<?php

namespace App\Services;

use App\Models\News;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
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
     * دریافت می‌کند. در صورت بروز خطا در حین دریافت اخبار، 
     * استثنای QueryException پرتاب می‌شود.
     *
     * @param int $perPage تعداد اخبار در هر صفحه
     * @param int|null $page شماره صفحه
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator محتوای صفحه‌بندی شده
     * 
     * @throws QueryException در صورت بروز خطا در حین دریافت اخبار
     */
    public function getAll(
        int $perPage,
        int $page,
    ) {
        try {
            $news = News::with('category')
                ->select('id', 'title', 'title_second', 'summary', 'body', 'image', 'tags', 'category_id', 'created_by')
                ->where('status', 'published')
                ->paginate($perPage, ['*'], 'page', $page);

            return $news;
        } catch (QueryException $e) {
            throw new Exception('error_to_fetch: ' . $e->getMessage());
        }
    }

    /**
     * دریافت یک خبر با شناسه مشخص
     *
     * این متد خبر با شناسه مشخص را از پایگاه داده دریافت می‌کند.
     * در صورت عدم وجود خبر با شناسه مشخص، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $newsId شناسه خبر
     * 
     * @return News
     * 
     * @throws ModelNotFoundException در صورت عدم وجود خبر
     */
    public function getById(int $id)
    {
        try {
            $news = News::select('id', 'title', 'title_second', 'summary', 'body', 'image', 'tags', 'category_id', 'created_by')
                ->where('status', 'published')
                ->findOrFail($id);

            return $news;
        } catch (ModelNotFoundException $e) {
            throw new Exception('news_not_found: ' . $e->getMessage());
        }
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
     * @throws QueryException در صورت بروز خطا در ذخیره‌سازی.
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
        try {
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
        } catch (QueryException $e) {
            throw new Exception('error_saving_news: ' . $e->getMessage());
        }
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
     * @throws QueryException در صورت بروز خطا در حین به‌روزرسانی.
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
        try {
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
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('not_found_news: ' . $e->getMessage());
        } catch (QueryException $e) {
            throw new Exception('error_update_news: ' . $e->getMessage());
        }
    }

    /**
     * حذف یک خبر با شناسه مشخص
     *
     * این متد خبر با شناسه مشخص را از پایگاه داده حذف می‌کند.
     *
     * @param int $newsId شناسه خبر
     * @return bool
     * @throws ModelNotFoundException در صورت عدم وجود خبر
     * @throws QueryException در صورت عدم مجوز کاربر
     */
    public function delete(int $id)
    {
        try {
            $news = $this->getById($id);

            if (!$news) {
                throw new ModelNotFoundException('not_found_news');
            }

            return $news->update(['status' => 'trashed']);
        } catch (QueryException $e) {
            throw new Exception('error_delete_news: ' . $e->getMessage());
        }
    }

    /**
     * بازیابی یک خبر حذف‌شده
     *
     * این متد خبر با شناسه مشخص را بازیابی می‌کند.
     *
     * @param int $newsId شناسه خبر
     * 
     * @throws ModelNotFoundException در صورت عدم وجود خبر
     * @throws QueryException در صورت عدم مجوز کاربر
     */
    public function restore($id)
    {
        try {
            $news = $this->getById($id);

            if (!$news) {
                throw new ModelNotFoundException('خبر پیدا نشد.');
            }

            $news->update(['status' => 'published']);
        } catch (QueryException $e) {
            throw new Exception('error_restore_news: ' . $e->getMessage());
        }
    }

    /**
     * بازگشت به نسخه‌ای قبلی از خبر.
     *
     * @param int $newsId شناسه خبر
     * @param int $revisionId شناسه فعالیت
     * 
     * @return News
     * 
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
            throw new Exception('revert_failed: ' . $e->getMessage());
        }
    }
}
