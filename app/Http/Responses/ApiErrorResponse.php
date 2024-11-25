<?php

namespace App\Http\Responses;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * کلاس ApiErrorResponse
 * 
 * این کلاس به منظور ارسال پاسخ‌های خطا به کلاینت استفاده می‌شود.
 * در هنگام بروز خطا، این کلاس یک پاسخ JSON تولید می‌کند که شامل 
 * اطلاعات مربوط به خطا، پیام و کد وضعیت HTTP می‌باشد.
 */
class ApiErrorResponse implements Responsable
{
    /**
     * سازنده کلاس ApiErrorResponse
     *
     * @param string $message پیام توضیح خطا (اجباری)
     * @param \Throwable|null $exception استثنای مربوط به خطا (اختیاری)
     * @param int $code کد وضعیت HTTP، پیش‌فرض 500 (خطای داخلی سرور)
     */
    public function __construct(
        private $message,
        private $exception = null,
        private $code = 500
    ) {}

    /**
     * تبدیل پاسخ به فرمت JSON
     *
     * این متد پاسخ خطا را به فرمت JSON تبدیل می‌کند و شامل 
     * اطلاعات مربوط به استثنا، پیام و کد وضعیت HTTP می‌باشد.
     *
     * @param \Illuminate\Http\Request $request درخواست ورودی
     * 
     * @return \Illuminate\Http\JsonResponse پاسخ JSON شامل اطلاعات خطا
     */
    public function toResponse($request)
    {
        $response = [
            'status' => 'error',
            'message' => $this->message,
        ];

        if ($this->exception) {
            $response['exception'] = [
                'exception_name' => class_basename(get_class($this->exception)),
                'exception_message' => $this->exception->getMessage(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ];

            // شناسایی نوع خطاها
            if ($this->exception instanceof QueryException) {
                $response['exception']['error_type'] = 'database_error';
                $this->code = 500;
            } elseif ($this->exception instanceof ValidationException) {
                $response['exception']['error_type'] = 'validation_error';
                $response['exception']['validation_exception'] = $this->exception->errors();
                $this->code = 422;
            } elseif ($this->exception instanceof AuthorizationException) {
                $response['exception']['error_type'] = 'authorization_error';
                $this->code = 403;
            } elseif ($this->exception instanceof NotFoundHttpException) {
                $response['exception']['error_type'] = 'not_found';
                $this->code = 404;
            } else {
                $response['exception']['error_type'] = 'general_error';
            }
        }

        return response()->json($response, $this->code);
    }
}
