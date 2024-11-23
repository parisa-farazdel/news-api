<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

/**
 * کلاس ApiErrorResponse
 * 
 * این کلاس به منظور ارسال پاسخ‌های خطا به کلاینت استفاده می‌شود.
 * در هنگام بروز خطا، این کلاس یک پاسخ JSON تولید می‌کند که شامل 
 * اطلاعات مربوط به خطا، پیام و کد وضعیت HTTP می‌باشد.
 */
class ApiErrorResponse implements Responsable
{
    private $errors;      // اطلاعات خطا
    private $message;     // پیام توضیح خطا
    private $statusCode;  // کد وضعیت HTTP

    /**
     * سازنده کلاس ApiErrorResponse
     *
     * @param mixed $errors اطلاعات خطا (اختیاری)
     * @param string $message پیام توضیح خطا (اختیاری)
     * @param int $statusCode کد وضعیت HTTP، پیش‌فرض 400 (خطا)
     */
    public function __construct(
        $errors = null,
        $message = '',
        $statusCode = 400
    ) {
        $this->errors = $errors;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    /**
     * تبدیل پاسخ به فرمت JSON
     *
     * @param \Illuminate\Http\Request $request درخواست ورودی
     * @return \Illuminate\Http\JsonResponse پاسخ JSON شامل اطلاعات خطا
     */
    public function toResponse($request)
    {
        $response = [
            'success' => false, // نشان‌دهنده عدم موفقیت
            'message' => $this->message, // پیام توضیح خطا
            'errors' => $this->errors // اطلاعات خطا
        ];

        return response()->json($response, $this->statusCode);
    }
}
