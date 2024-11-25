<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Category\CreateCategoryDTO;
use App\DTOs\Category\UpdateCategoryDTO;
use App\DTOs\PaginateDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس CategoryController
 * 
 * کنترلر مربوط به دسته‌بندی‌ها که وظیفه مدیریت عملیات CRUD را بر عهده دارد.
 */
class CategoryController extends Controller
{
    protected $categoryService;


    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * دریافت تمام دسته‌بندی‌ها.
     *
     * این متد تمامی دسته‌بندی‌ها را با توجه به پارامترهای صفحه‌بندی
     * از سرویس دسته‌بندی دریافت کرده و آن‌ها را برمی‌گرداند.
     *
     * @param Request $request اطلاعات درخواست شامل پارامترهای صفحه‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت دسته‌بندی‌ها
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $categories = $this->categoryService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($categories, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * دریافت اخبار مربوط به یک دسته‌بندی خاص.
     *
     * این متد اخبار مرتبط با شناسه دسته‌بندی مشخص شده را با توجه به
     * پارامترهای صفحه‌بندی دریافت کرده و برمی‌گرداند.
     *
     * @param Request $request اطلاعات درخواست شامل پارامترهای صفحه‌بندی
     * @param int $categoryId شناسه دسته‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت اخبار
     */
    public function news(Request $request, $categoryId)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page', 10),
            $request->input('page', 1),
        );

        try {
            $news = $this->categoryService->news($categoryId, $paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($news, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * ایجاد یک دسته‌بندی جدید.
     *
     * این متد اطلاعات دسته‌بندی جدید را از درخواست دریافت کرده و
     * آن را به سرویس دسته‌بندی ارسال می‌کند تا ذخیره شود.
     *
     * @param Request $request اطلاعات درخواست شامل عنوان، شناسه والد و وضعیت
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند ایجاد دسته‌بندی
     */
    public function create(Request $request)
    {
        $categoryDTO = new CreateCategoryDTO(
            $request->input('title'),
            $request->input('parent_id'),
            $request->input('status'),
        );

        try {
            $category = $this->categoryService->store(
                $categoryDTO->title,
                $categoryDTO->parentId,
                $categoryDTO->status,
                Auth::id(),
            );

            return new ApiSuccessResponse($category, 'create_success', Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return new ApiErrorResponse('create_error', $exception);
        }
    }

    /**
     * به‌روزرسانی یک دسته‌بندی موجود.
     *
     * این متد اطلاعات جدید دسته‌بندی را از درخواست دریافت کرده و
     * آن را به سرویس دسته‌بندی ارسال می‌کند تا به‌روزرسانی شود.
     *
     * @param Request $request اطلاعات درخواست شامل عنوان، شناسه والد و وضعیت
     * @param int $id شناسه دسته‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند به‌روزرسانی دسته‌بندی
     */
    public function update(Request $request, $id): ApiSuccessResponse | ApiErrorResponse
    {
        try {
            $categoryDTO = new UpdateCategoryDTO(
                $id,
                $request->input('title'),
                $request->input('parent_id'),
                $request->input('status'),
            );

            $updatedCategory = $this->categoryService->update(
                $categoryDTO->categoryId,
                $categoryDTO->title,
                $categoryDTO->parentId,
                $categoryDTO->status,
                Auth::id(),
            );

            return new ApiSuccessResponse($updatedCategory, 'update_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('update_error', $exception);
        }
    }

    /**
     * حذف یک دسته‌بندی.
     *
     * این متد شناسه دسته‌بندی را دریافت کرده و آن را از سیستم حذف می‌کند.
     *
     * @param int $id شناسه دسته‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند حذف دسته‌بندی
     */
    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);

            return new ApiSuccessResponse(null, 'delete_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('delete_error', $exception);
        }
    }

    /**
     * بازیابی یک دسته‌بندی حذف شده.
     *
     * این متد شناسه دسته‌بندی را دریافت کرده و آن را از حالت حذف
     * بازیابی می‌کند.
     *
     * @param int $id شناسه دسته‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند بازیابی دسته‌بندی
     */
    public function restore($id)
    {
        try {
            $news = $this->categoryService->restore($id);

            return new ApiSuccessResponse($news, 'restore_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('restore_error', $exception);
        }
    }
}
