<?php

namespace App\Services;

use App\Models\Category;
use App\Models\News;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
     * این متد لیستی از دسته‌بندی‌ها را با توجه به تعداد مورد نظر در هر صفحه 
     * و شماره صفحه ارائه می‌دهد. تنها دسته‌بندی‌هایی که وضعیت آنها 'published' 
     * است، بازگردانده می‌شوند.
     *
     * @param int $perPage تعداد دسته‌بندی‌ها در هر صفحه
     * @param int|null $page شماره صفحه
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator مجموعه‌ای از دسته‌بندی‌ها
     */
    public function getAll(
        int $perPage,
        int $page,
    ) {
        $categories = Category::select('id', 'parent_id', 'title')
            ->where('status', 'published')
            ->paginate($perPage, ['*'], 'page', $page);

        return $categories;
    }

    /**
     * دریافت اخبار مربوط به یک دسته‌بندی خاص.
     *
     * این متد اخبار مرتبط با شناسه دسته‌بندی مشخص شده را با قابلیت 
     * صفحه‌بندی بازمی‌گرداند. در صورتی که دسته‌بندی وجود نداشته باشد، 
     * استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $categoryId شناسه دسته‌بندی
     * @param int $perPage تعداد اخبار در هر صفحه
     * @param int|null $page شماره صفحه
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator مجموعه‌ای از اخبار
     * 
     * @throws ModelNotFoundException در صورت عدم وجود دسته‌بندی
     */
    public function news(
        int $categoryId,
        int $perPage,
        int $page,
    ) {
        $category = Category::findOrFail($categoryId);

        if (!$category) {
            throw new ModelNotFoundException('category_not_found');
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
     * در صورت موفقیت، دسته‌بندی جدید ایجاد شده را برمی‌گرداند.
     *
     * @param string $title عنوان دسته‌بندی
     * @param string|null $parentId شناسه والد دسته‌بندی
     * @param string|null $status وضعیت دسته‌بندی (اختیاری)
     * @param int $createdBy شناسه کاربری که دسته‌بندی را ایجاد کرده است
     * 
     * @return Category دسته‌بندی جدید ایجاد شده
     * 
     * @throws ModelNotFoundException در صورت عدم وجود والد دسته‌بندی
     * @throws QueryException در صورت بروز خطا در حین ذخیره‌سازی
     * @throws QueryException در صورت عدم مجوز یا خطای کوئری 
     */
    public function store(
        string $title,
        string $parentId,
        string $status = null,
        int $createdBy,
    ): Category {
        if ($parentId && !Category::find($parentId)) {
            throw new ModelNotFoundException('parent_category_not_found');
        }

        $data = [
            'title' => $title,
            'parent_id' => $parentId,
            'status' => $status,
            'created_by' => $createdBy,
        ];

        try {
            return Category::create(array_filter($data));
        } catch (QueryException $e) {
            throw new Exception('error_saving_category: ' . $e->getMessage());
        }
    }

    /**
     * به‌روزرسانی اطلاعات یک دسته‌بندی.
     *
     * این متد اطلاعات یک دسته‌بندی موجود را به‌روزرسانی می‌کند. در صورت عدم 
     * وجود دسته‌بندی با شناسه مشخص شده، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $id شناسه دسته‌بندی
     * @param string|null $title عنوان جدید دسته‌بندی
     * @param int|null $parent_id شناسه والد جدید دسته‌بندی
     * @param string|null $status وضعیت جدید دسته‌بندی
     * @param int $updatedBy شناسه کاربری که دسته‌بندی را به‌روزرسانی کرده است
     * 
     * @return Category دسته‌بندی به‌روزرسانی شده
     * 
     * @throws ModelNotFoundException در صورت عدم وجود دسته‌بندی
     * @throws QueryException در صورت بروز خطا در حین به‌روزرسانی
     */
    public function update(
        int $id,
        ?string $title,
        ?int $parent_id,
        ?string $status,
        int $updatedBy,
    ) {
        try {
            $category = Category::findOrFail($id);

            $data = [
                'title' => $title,
                'parent_id' => $parent_id,
                'status' => $status,
                'updated_by' => $updatedBy,
            ];

            $category->update(array_filter($data));

            return $category->fresh();
        } catch (QueryException $e) {
            throw new Exception('error_update_category: ' . $e->getMessage());
        }
    }

    /**
     * حذف یک دسته‌بندی.
     *
     * این متد یک دسته‌بندی را به‌طور منطقی حذف می‌کند. در صورت عدم وجود 
     * دسته‌بندی با شناسه مشخص شده، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $id شناسه دسته‌بندی
     * 
     * @return bool نتیجه عملیات حذف
     * 
     * @throws ModelNotFoundException در صورت عدم وجود دسته‌بندی
     * @throws QueryException در صورت بروز خطا در حین حذف
     */
    public function delete(int $id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->update(['status' => 'trashed']);
            $category->delete();

            return true;
        } catch (QueryException $e) {
            throw new Exception('error_delete_category: ' . $e->getMessage());
        }
    }

    /**
     * بازیابی یک دسته‌بندی حذف شده.
     *
     * این متد وضعیت یک دسته‌بندی را به 'published' تغییر می‌دهد. 
     * در صورت عدم وجود دسته‌بندی با شناسه مشخص شده،
     * استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $id شناسه دسته‌بندی
     * 
     * @throws ModelNotFoundException در صورت عدم وجود دسته‌بندی
     * @throws QueryException در صورت بروز خطا در حین بازیابی
     */
    public function restore($id)
    {
        try {
            $category = Category::findOrFail($id);

            if (!$category) {
                throw new ModelNotFoundException('category_not_found');
            }

            $category->update(['status' => 'published']);
        } catch (QueryException $e) {
            throw new Exception('error_restore_category: ' . $e->getMessage());
        }
    }
}
