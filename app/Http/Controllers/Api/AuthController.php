<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\LoginDTO;
use App\DTOs\User\LogoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس AuthController
 * 
 * این کلاس مسئول مدیریت عملیات احراز هویت کاربران شامل 
 * لاگین، لاگ‌اوت و به‌روزرسانی اطلاعات کاربر می‌باشد.
 */
class AuthController extends Controller
{
    protected $authService;

    /**
     * سازنده کلاس AuthController
     *
     * @param AuthService $authService شی سرویس احراز هویت
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * ورود کاربر با استفاده از اطلاعات درخواست.
     *
     * @param Request $request اطلاعات درخواست شامل ایمیل و رمز عبور
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * @throws Exception در صورت بروز خطا در فرآیند ورود
     */
    public function login(Request $request)
    {
        try {
            $authDTO = new LoginDTO(
                $request->input('email'),
                $request->input('password'),
            );

            // ارسال درخواست به سرویس
            $user = $this->authService->login(
                $authDTO->email,
                $authDTO->password
            );

            // بررسی نتیجه و ارسال پاسخ مناسب
            if ($user) {
                return new ApiSuccessResponse($user);
            }
        } catch (Exception $e) {
            return new ApiErrorResponse('login_failed' . $e->getmessage());
        }
    }


    /**
     * خروج کاربر.
     *
     * @return ApiSuccessResponse|ApiErrorResponse پاسخ موفق یا خطا
     * @throws Exception در صورت بروز خطا در فرآیند خروج
     */
    public function logout()
    {
        try {
            $userId = Auth::id();

            $logoutDTO = new LogoutDTO($userId);

            $this->authService->logout($logoutDTO);

            return new ApiSuccessResponse(null);
        } catch (Exception $e) {
            return new ApiErrorResponse('logout_failed' . $e->getmessage());
        }
    }
}
