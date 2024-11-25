<?php

namespace App\Http\Controllers\Api;

use App\DTOs\PaginateDTO;
use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * کنترلر مربوط به کاربران.
 *
 * این کلاس مسئول مدیریت عملیات مربوط به کاربران از قبیل
 * دریافت، ایجاد، به‌روزرسانی، حذف و بازیابی کاربران است.
 */
class UserController extends Controller
{
    protected $userService;

    /**
     * سازنده کلاس UserController
     *
     * @param UserService $userService سرویس مدیریت کاربران
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * دریافت تمام کاربران.
     *
     * این متد تمامی کاربران را با توجه به پارامترهای صفحه‌بندی
     * از سرویس کاربران دریافت کرده و آن‌ها را برمی‌گرداند.
     *
     * @param Request $request اطلاعات درخواست شامل پارامترهای صفحه‌بندی
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت کاربران
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $users = $this->userService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($users, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * نمایش اطلاعات یک کاربر.
     *
     * این متد اطلاعات کاربر مشخص شده را بر اساس شناسه دریافت کرده و
     * آن را برمی‌گرداند. در صورت عدم وجود شناسه، اطلاعات کاربر
     * جاری را برمی‌گرداند.
     *
     * @param int|null $id شناسه کاربر (اختیاری)
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند دریافت کاربر
     */
    public function show($id)
    {
        if ($id === null) {
            $id = Auth::id();
        }

        try {
            $user = $this->userService->getById($id);
            return new ApiSuccessResponse($user, 'fetch_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('fetch_error', $exception);
        }
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * این متد اطلاعات کاربر جدید را از درخواست دریافت کرده و
     * آن را به سرویس کاربران ارسال می‌کند تا ذخیره شود.
     *
     * @param Request $request اطلاعات درخواست 
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند ایجاد کاربر
     */
    public function create(Request $request)
    {
        $userDTO = new CreateUserDTO(
            $request->input('username'),
            $request->input('first_name'),
            $request->input('last_name'),
            $request->input('email'),
            $request->input('password'),
        );

        try {
            $user = $this->userService->store(
                $userDTO->userName,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->email,
                $userDTO->password,
            );

            return new ApiSuccessResponse($user, 'create_success', Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return new ApiErrorResponse('create_error', $exception);
        }
    }

    /**
     * به‌روزرسانی اطلاعات یک کاربر موجود.
     *
     * این متد اطلاعات جدید کاربر را از درخواست دریافت کرده و
     * آن را به سرویس کاربران ارسال می‌کند تا به‌روزرسانی شود.
     *
     * @param Request $request اطلاعات درخواست 
     * @param int|null $id شناسه کاربر (اختیاری)
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند به‌روزرسانی کاربر
     */
    public function update(Request $request, $id): ApiSuccessResponse | ApiErrorResponse
    {
        if ($id === null) {
            $id = Auth::id();
        }

        try {
            $userDTO = new UpdateUserDTO(
                $id,
                $request->input('username'),
                $request->input('first_name'),
                $request->input('last_name'),
                $request->input('email'),
                $request->input('password'),
            );

            $updatedUser = $this->userService->update(
                $userDTO->userId,
                $userDTO->userName,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->email,
                $userDTO->password,
            );

            return new ApiSuccessResponse($updatedUser, 'update_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('update_error', $exception);
        }
    }

    /**
     * حذف یک کاربر.
     *
     * این متد شناسه کاربر را دریافت کرده و آن را از سیستم حذف می‌کند.
     *
     * @param int $id شناسه کاربر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند حذف کاربر
     */
    public function destroy($id)
    {
        try {
            $this->userService->delete($id);

            return new ApiSuccessResponse(null, 'delete_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('delete_error', $exception);
        }
    }

    /**
     * بازیابی یک کاربر حذف شده.
     *
     * این متد شناسه کاربر را دریافت کرده و آن را از حالت حذف
     * بازیابی می‌کند.
     *
     * @param int $id شناسه کاربر
     * 
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * 
     * @throws Exception در صورت بروز خطا در فرآیند بازیابی کاربر
     */
    public function restore($id)
    {
        try {
            $user = $this->userService->restore($id);

            return new ApiSuccessResponse($user, 'restore_success');
        } catch (Exception $exception) {
            return new ApiErrorResponse('restore_error', $exception);
        }
    }
}
