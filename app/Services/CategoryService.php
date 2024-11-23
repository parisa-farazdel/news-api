<?php

namespace App\Services;

use App\Models\Category;
use App\Models\News;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس CategoryService
 * 
 * سرویس مربوط به مدیریت دسته‌بندی‌ها که شامل عملیات CRUD است.
 */
class CategoryService
{
    /**
     * دریافت همه دسته‌بندی‌ها با قابلیت صفحه‌بندی.
     *
     * @param int $perPage تعداد دسته‌بندی‌ها در هر صفحه
     * @param int|null $page شماره صفحه
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $perPage = 10,
        int $page = null,
    ) {
        $categories = Category::select('id', 'parent_id', 'title')
            ->where('status', 'published')
            ->paginate($perPage, ['*'], 'page', $page);

        return $categories;
    }

    /**
     * دریافت اخبار مربوط به یک دسته‌بندی خاص.
     *
     * @param int $categoryId شناسه دسته‌بندی
     * @param int $perPage تعداد اخبار در هر صفحه
     * @param int|null $page شماره صفحه
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws Exception در صورت عدم وجود دسته‌بندی
     */
    public function news(
        int $categoryId,
        int $perPage = 10,
        int $page = null,
    ) {
        $category = Category::findOrFail($categoryId);

        if (!$category) {
            throw new Exception('category not found');
        }

        $news = News::with('category')
            ->select('title', 'category_id', 'title_second', 'summary', 'body', 'image', 'tags')
            ->where([
                ['category_id', $categoryId],
                ['status', 'published'],
            ],)
            ->paginate($perPage, ['title', 'category_id', 'title_second', 'summary', 'body', 'image', 'tags'], 'page', $page);

        return $news;
    }

    /**
     * ذخیره‌سازی یک دسته‌بندی جدید.
     *
     * این متد اطلاعات دسته‌بندی را دریافت کرده و آن را در پایگاه داده ذخیره می‌کند.
     *
     * @param string $title عنوان دسته‌بندی.
     * @param string $parentId شناسه والد دسته‌بندی.
     * @param string|null $status وضعیت دسته‌بندی (اختیاری).
     * @param int $createdBy شناسه کاربری که دسته‌بندی را ایجاد کرده است.
     *
     * @return Category دسته‌بندی جدید ایجاد شده.
     *
     * @throws Exception در صورتی که کاربر مجاز نباشد.
     */
    public function store(
        string $title,
        string $parentId,
        string $status = null,
        int $createdBy,
    ): Category {
        $user = Auth::user();

        if (!$user || $user->role != 'admin') {
            throw new Exception('unauthorized');
        }

        $data = [
            'title' => $title,
            'parent_id' => $parentId,
            'status' => $status,
            'created_by' => $user->id,
            'created_by' => $createdBy,
        ];

        return Category::create(array_filter($data));
    }

    /**
     * به‌روزرسانی اطلاعات یک دسته‌بندی موجود.
     *
     * این متد اطلاعات دسته‌بندی را بر اساس شناسه آن به‌روزرسانی می‌کند.
     *
     * @param int $categoryId شناسه دسته‌بندی که باید به‌روزرسانی شود.
     * @param string|null $title عنوان جدید دسته‌بندی.
     * @param int|null $parent_id شناسه والد جدید دسته‌بندی.
     * @param string|null $status وضعیت جدید دسته‌بندی.
     * @param int $updatedBy شناسه کاربری که دسته‌بندی را به‌روزرسانی کرده است.
     *
     * @return Category دسته‌بندی به‌روزرسانی شده.
     *
     * @throws Exception در صورتی که کاربر مجاز نباشد یا دسته‌بندی پیدا نشود.
     */
    public function update(
        int $categoryId,
        ?string $title,
        ?int $parent_id,
        ?string $status,
        int $updatedBy,
    ) {
        $user = Auth::user();

        if (!$user || $user->role != 'admin') {
            throw new Exception('unauthorized');
        }

        $category = Category::findOrFail($categoryId);

        $data = [
            'title' => $title,
            'first_name' => $parent_id,
            'status' => $status,
            'updated_by' => $user->id,
            'updated_by' => $user->id,
            'updated_by' => $updatedBy,
        ];

        $category->update(array_filter($data));

        return $category->fresh();
    }

    /**
     * حذف یک دسته‌بندی.
     *
     * @param int $categoryId شناسه دسته‌بندی
     * @return int تعداد دسته‌بندی‌های حذف شده
     * @throws Exception در صورت عدم مجوز
     */
    public function delete(int $categoryId)
    {
        $user = Auth::user();

        if (!$user || $user->role != 'admin') {
            throw new Exception('unauthorized');
        }

        $category = Category::where(['id' => $categoryId])->findOrFail($categoryId);

        $category->update(['status' => 'trashed']);

        $category->delete();

        return true;
    }

    /**
     * بازیابی یک دسته‌بندی حذف شده.
     *
     * @param int $categoryId شناسه دسته‌بندی
     * @throws Exception در صورت عدم مجوز یا عدم وجود دسته‌بندی
     */
    public function restore($categoryId)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            throw new Exception('unauthorized');
        }

        $category = Category::withTrashed()->find($categoryId);

        if (!$category) {
            throw new Exception('category_not_found');
        }

        $category->update(['status' => 'published']);

        $category->restore();
    }
}
