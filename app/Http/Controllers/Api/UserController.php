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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * 
 * این کلاس شامل متدهایی برای مدیریت کاربران است.
 * متدها شامل ایجاد، دریافت، بروزرسانی و حذف کاربران می‌باشند.
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
     * دریافت لیست کاربران.
     *
     * @param Request $request درخواست حاوی پارامترهای جستجو
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function getAll(Request $request)
    {
        $paginateDTO = new PaginateDTO(
            $request->input('per_page'),
            $request->input('page'),
        );

        try {
            $users = $this->userService->getAll($paginateDTO->perPage, $paginateDTO->page);

            return new ApiSuccessResponse($users);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_fetch' . $e->getMessage(), 400);
        }
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param Request $request درخواست حاوی اطلاعات کاربر جدید
     * @return ApiSuccessResponse | ApiErrorResponse
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

            return new ApiSuccessResponse($user, 201);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_registration' . $e->getMessage(), 400);
        }
    }

    /**
     * حذف یک کاربر.
     *
     * @param int $userId شناسه کاربر
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function destroy($id)
    {
        try {
            $this->userService->delete($id);

            return new ApiSuccessResponse(null);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_delete', 404);
        }
    }

    /**
     * بازیابی یک کاربر حذف‌شده
     *
     * این متد کاربر با شناسه مشخص را بازیابی می‌کند.
     *
     * @param int $id شناسه کاربر
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     */
    public function restore($id)
    {
        try {
            $user = $this->userService->restore($id);

            return new ApiSuccessResponse($user);
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_restore', 403);
        }
    }

    /**
     * نمایش پروفایل کاربر احراز هویت شده.
     *
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function showProfile()
    {
        $user = Auth::id();
        try {
            $user = $this->userService->getById($user);
            return new ApiSuccessResponse($user, 'success_retrieved');
        } catch (Exception $e) {
            return new ApiErrorResponse('unauthenticated', 404);
        }
    }

    /**
     * نمایش یک کاربر خاص.
     *
     * @param int $userId شناسه کاربر
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getById($id);
            return new ApiSuccessResponse($user, 'success_retrieved');
        } catch (Exception $e) {
            return new ApiErrorResponse('failed_fetch', 404);
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر.
     *
     * @param Request $request درخواست حاوی اطلاعات جدید کاربر
     * @param int|null $userId شناسه کاربر (اختیاری)
     * @return ApiSuccessResponse | ApiErrorResponse
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

            return new ApiSuccessResponse($updatedUser, 'success');
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('not_found_user', 404);
        } catch (Exception $e) {
            return new ApiErrorResponse('update_failed' . $e->getMessage(), 400);
        }
    }
}
