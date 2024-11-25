<?php

namespace App\Http\Controllers\Api;

use App\DTOs\News\CreateNewsDTO;
use App\DTOs\News\UpdateNewsDTO;
use App\DTOs\PaginateDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\NewsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * دریافت تمام اخبار.
     *
     * این متد تمامی اخبار را با توجه به پارامترهای صفحه‌بندی
     * از سرویس اخبار دریافت کرده و آن‌ها را برمی‌گرداند.
     *
     * @param Request $request اطلاعات درخواست شامل پارامترهای صفحه‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت اخبار
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $news = $this->newsService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($news, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * نمایش اخبار بر اساس شناسه.
     *
     * این متد خبر مشخص شده را بر اساس شناسه دریافت کرده و
     * آن را برمی‌گرداند.
     *
     * @param int $id شناسه خبر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت خبر
     */
    public function show(int $id)
    {
        try {
            $news = $this->newsService->getById($id);
            return new ApiSuccessResponse($news, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * ایجاد یک خبر جدید.
     *
     * این متد اطلاعات خبر جدید را از درخواست دریافت کرده و
     * آن را به سرویس اخبار ارسال می‌کند تا ذخیره شود.
     *
     * @param Request $request اطلاعات درخواست 
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند ایجاد خبر
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

            return new ApiSuccessResponse($news, 'create_success', Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return new ApiErrorResponse('create_error', $exception);
        }
    }

    /**
     * به‌روزرسانی یک خبر موجود.
     *
     * این متد اطلاعات جدید خبر را از درخواست دریافت کرده و
     * آن را به سرویس اخبار ارسال می‌کند تا به‌روزرسانی شود.
     *
     * @param Request $request اطلاعات درخواست
     * @param int $newsId شناسه خبر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند به‌روزرسانی خبر
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

            return new ApiSuccessResponse($updatedNews, 'update_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('update_error', $exception);
        }
    }

    /**
     * حذف یک خبر.
     *
     * این متد شناسه خبر را دریافت کرده و آن را از سیستم حذف می‌کند.
     *
     * @param int $id شناسه خبر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند حذف خبر
     */
    public function destroy($id)
    {
        try {
            $this->newsService->delete($id);

            return new ApiSuccessResponse(null, 'delete_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('delete_error', $exception);
        }
    }

    /**
     * بازیابی یک خبر حذف شده.
     *
     * این متد شناسه خبر را دریافت کرده و آن را از حالت حذف
     * بازیابی می‌کند.
     *
     * @param int $id شناسه خبر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند بازیابی خبر
     */
    public function restore($id)
    {
        try {
            $news = $this->newsService->restore($id);

            return new ApiSuccessResponse($news, 'restore_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('restore_error', $exception);
        }
    }

    /**
     * بازگشت به نسخه قبلی یک خبر.
     *
     * این متد شناسه خبر و شناسه نسخه را دریافت کرده و
     * خبر را به نسخه قبلی خود برمی‌گرداند.
     *
     * @param Request $request اطلاعات درخواست شامل شناسه نسخه
     * @param int $id شناسه خبر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند بازگشت به نسخه
     */
    public function revert(Request $request, $id)
    {
        try {
            $news = $this->newsService->revertToRevision(
                $id,
                $request->input('revision_id'),
            );
            return new ApiSuccessResponse($news, 'revert_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('revert_error', $exception);
        }
    }
}
