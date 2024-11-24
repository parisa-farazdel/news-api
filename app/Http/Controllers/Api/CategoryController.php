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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس CategoryController
 * 
 * کنترلر مربوط به دسته‌بندی‌ها که وظیفه مدیریت عملیات CRUD را بر عهده دارد.
 */
class CategoryController extends Controller
{
    protected $categoryService;

    /**
     * CategoryController متد سازنده.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * دریافت همه دسته‌بندی‌ها.
     *
     * @param Request $request
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $categories = $this->categoryService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($categories);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_to_fetch_news' . $e->getMessage(), 400);
        }
    }

    /**
     * دریافت اخبار مربوط به یک دسته‌بندی خاص.
     *
     * @param Request $request
     * @param int $categoryId
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function news(Request $request, $categoryId)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page', 10),
            $request->input('page', 1),
        );

        try {
            $news = $this->categoryService->news($categoryId, $paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($news);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_to_fetch_news' . $e->getMessage(), 400);
        }
    }

    /**
     * ایجاد یک دسته‌بندی جدید.
     *
     * @param Request $request
     * @return ApiSuccessResponse | ApiErrorResponse
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

            return new ApiSuccessResponse($category, 201);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_creation' . $e->getMessage(), 400);
        }
    }

    /**
     * به‌روزرسانی یک دسته‌بندی موجود.
     *
     * @param Request $request
     * @param int $categoryId
     * @return ApiSuccessResponse | ApiErrorResponse
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

            return new ApiSuccessResponse($updatedCategory);
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('not_found_category', 404);
        } catch (Exception $e) {
            return new ApiErrorResponse('update_failed' . $e->getMessage(), 400);
        }
    }

    /**
     * حذف یک دسته‌بندی.
     *
     * @param int $categoryId
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);

            return new ApiSuccessResponse(null);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_delete', 404);
        }
    }

    /**
     * بازیابی یک دسته‌بندی حذف شده.
     *
     * @param int $categoryId
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function restore($id)
    {
        try {
            $news = $this->categoryService->restore($id);

            return new ApiSuccessResponse($news);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_restore', 403);
        }
    }
}
