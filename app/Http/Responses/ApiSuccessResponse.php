<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

/**
 * کلاس ApiSuccessResponse
 * 
 * این کلاس به منظور ارسال پاسخ‌های موفقیت‌آمیز به کلاینت استفاده می‌شود.
 * در صورت موفقیت‌آمیز بودن عملیات، این کلاس یک پاسخ JSON تولید می‌کند 
 * که شامل اطلاعات مربوط به موفقیت، پیام و داده‌های مرتبط می‌باشد.
 */
class ApiSuccessResponse implements Responsable
{
    private $data;        // داده‌های مربوط به پاسخ
    private $message;     // پیام موفقیت
    private $statusCode;  // کد وضعیت HTTP

    /**
     * سازنده کلاس ApiSuccessResponse
     *
     * @param mixed $data داده‌های مربوط به پاسخ (اختیاری)
     * @param string $message پیام موفقیت (اختیاری)
     * @param int $statusCode کد وضعیت HTTP، پیش‌فرض 200 (موفقیت)
     */
    public function __construct(
        $data = null,
        $message = 'Operation successful',
        $statusCode = 200
    ) {
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    /**
     * تبدیل پاسخ به فرمت JSON
     *
     * @param \Illuminate\Http\Request $request درخواست ورودی
     * @return \Illuminate\Http\JsonResponse پاسخ JSON شامل اطلاعات موفقیت
     */
    public function toResponse($request)
    {
        $response = [
            'success' => true,  // نشان‌دهنده موفقیت
            'message' => $this->message, // پیام موفقیت
            'data' => $this->data // داده‌های مربوط به پاسخ
        ];

        return response()->json($response, $this->statusCode);
    }
}
