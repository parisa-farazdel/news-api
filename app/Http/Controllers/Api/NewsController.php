<?php

namespace App\Http\Controllers\Api;

use App\DTOs\News\CreateNewsDTO;
use App\DTOs\News\UpdateNewsDTO;
use App\DTOs\PaginateDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\News;
use App\Services\NewsService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * کنترلر اخبار
 *
 * این کلاس مسئول مدیریت عملیات مختلف مربوط به اخبار از جمله
 * ایجاد، نمایش، به‌روزرسانی و حذف اخبار است.
 */
class NewsController extends Controller
{
    protected $newsService;

    /**
     * سازنده کلاس NewsController
     *
     * @param NewsService $newsService سرویس مدیریت اخبار
     */
    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * دریافت تمامی اخبار
     *
     * این متد تمامی اخبار را با توجه به پارامترهای صفحه‌بندی دریافت می‌کند.
     *
     * @param Request $request درخواست HTTP
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $news = $this->newsService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($news, 'success_retrieved');
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_fetch' . $e->getMessage(), 400);
        }
    }

    /**
     * نمایش یک خبر خاص
     *
     * این متد خبر با شناسه مشخص را نمایش می‌دهد.
     *
     * @param int $newsId شناسه خبر
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function show(int $id)
    {
        try {
            $news = $this->newsService->getById($id);
            return new ApiSuccessResponse($news, 'success_retrieved');
        } catch (Exception $e) {
            return new ApiErrorResponse('not_found.', 404);
        }
    }

    /**
     * ایجاد یک خبر جدید
     *
     * این متد یک خبر جدید را بر اساس اطلاعات دریافتی از کاربر ایجاد می‌کند.
     *
     * @param Request $request درخواست HTTP
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function create(Request $request)
    {
        $newsDTO = new CreateNewsDTO(
            $request->input('title'),
            $request->input('category_id'),
            $request->input('title_second'),
            $request->input('slug'),
            $request->input('summary'),
            $request->input('body'),
            $request->file('image'),
            $request->input('tags'),
            $request->input('status'),
        );

        try {
            $news = $this->newsService->store(
                $newsDTO->title,
                $newsDTO->categoryId,
                $newsDTO->titleSecond,
                $newsDTO->slug,
                $newsDTO->summary,
                $newsDTO->body,
                $newsDTO->image,
                $newsDTO->tags,
                $newsDTO->status,
                Auth::id(),
            );

            return new ApiSuccessResponse($news, 'success_created', 201);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_creation' . $e->getMessage(), 400);
        }
    }

    /**
     * به‌روزرسانی یک خبر 
     *
     * این متد اطلاعات خبر با شناسه مشخص را به‌روزرسانی می‌کند.
     *
     * @param Request $request درخواست HTTP
     * @param int $newsId شناسه خبر
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function update(Request $request, $newsId): ApiSuccessResponse | ApiErrorResponse
    {
        try {
            $newsDTO = new UpdateNewsDTO(
                $newsId,
                $request->input('title'),
                $request->input('category_id'),
                $request->input('title_second'),
                $request->input('slug'),
                $request->input('summary'),
                $request->input('body'),
                $request->file('image'),
                $request->input('tags'),
                $request->input('status'),
            );

            $updatedNews = $this->newsService->update(
                $newsId,
                $newsDTO->title,
                $newsDTO->categoryId,
                $newsDTO->titleSecond,
                $newsDTO->slug,
                $newsDTO->summary,
                $newsDTO->body,
                $newsDTO->image,
                $newsDTO->tags,
                $newsDTO->status,
                Auth::id(),
            );

            return new ApiSuccessResponse($updatedNews, 'success_updated');
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('not_found_user', 404);
        } catch (Exception $e) {
            return new ApiErrorResponse('update_failed' . $e->getMessage(), 400);
        }
    }

    /**
     * حذف یک خبر
     *
     * این متد خبر با شناسه مشخص را حذف می‌کند.
     *
     * @param int $newsId شناسه خبر
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function destroy($id)
    {
        try {
            $this->newsService->delete($id);

            return new ApiSuccessResponse(null, 'success_deleted');
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_delete', 403);
        }
    }

    /**
     * بازیابی یک خبر حذف‌شده
     *
     * این متد خبر با شناسه مشخص را بازیابی می‌کند.
     *
     * @param int $newsId شناسه خبر
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function restore($newsId)
    {
        try {
            $news = $this->newsService->restore($newsId);

            return new ApiSuccessResponse($news, 'success_restored');
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_restore', 403);
        }
    }

    /**
     * بازگرداندن یک خبر به نسخه‌ای از قبل
     *
     * @param \Illuminate\Http\Request $request
     * @param int $newsId شناسه خبری که قرار است به نسخه قبلی بازگردانده شود
     * @return \App\Http\Resources\ApiSuccessResponse|\App\Http\Resources\ApiErrorResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException اگر خبری با شناسه داده شده پیدا نشود
     * @throws \Exception اگر در فرآیند بازگرداندن به نسخه قبلی مشکلی رخ دهد
     */
    public function revert(Request $request, $newsId)
    {
        try {
            $news = $this->newsService->revertToRevision(
                $newsId,
                $request->input('revision_id'),
            );
            return new ApiSuccessResponse($news, 'success_reverted');
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('not_found', 404);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_revert' . $e->getMessage(), 403);
        }
    }
}
