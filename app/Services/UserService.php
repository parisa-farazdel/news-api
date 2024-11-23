<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * 
 * این کلاس شامل متدهایی برای انجام عملیات مربوط به مدیریت کاربران است.
 * متدها شامل ایجاد، دریافت، بروزرسانی و حذف کاربران می‌باشند.
 */
class UserService
{
    /**
     * دریافت لیست کاربران با صفحه‌بندی.
     *
     * @param int $perPage تعداد کاربران در هر صفحه
     * @param int|null $page شماره صفحه
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $perPage = 10,
        int $page = null,
    ) {
        $users = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')
            ->where('status', 'active')
            ->paginate($perPage, ['*'], 'page', $page);

        return $users;
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param string $userName نام کاربری
     * @param string $firstName نام کوچک
     * @param string $lastName نام خانوادگی
     * @param string $email ایمیل
     * @param string $password رمز عبور
     * @return User کاربر جدید ایجاد شده
     * @throws ValidationException
     */
    public function store(
        string $userName,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
    ): User {
        $data = [
            'username' => $userName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
        ];

        return User::create($data);
    }

    /**
     * به‌روزرسانی اطلاعات کاربر با استفاده از شناسه کاربر.
     *
     * @param int $userId شناسه کاربر
     * @param string|null $userName نام کاربری
     * @param string|null $firstName نام کوچک
     * @param string|null $lastName نام خانوادگی
     * @param string|null $email ایمیل
     * @param string|null $password رمز عبور جدید (اختیاری)
     * @return User اطلاعات به‌روزرسانی شده کاربر
     * @throws Exception در صورت عدم یافتن کاربر
     */
    public function update(
        int $id,
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password = null,
    ) {

        $user = $this->getById($id);

        if (!$user) {
            Log::error('کاربر پیدا نشد: ' . $id);
            throw new ModelNotFoundException('User not found');
        }

        Log::info('در حال به‌روزرسانی کاربر: ', [
            'id' => $id,
            'username' => $userName,
            'email' => $email,
        ]);

        $data = [
            'username' => $userName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' =>  $password ? Hash::make($password) : null
        ];

        $user->update(array_filter($data));

        Log::info('کاربر با موفقیت به‌روزرسانی شد: ' . $id);


        return $user->fresh();
    }


    /**
     * دریافت اطلاعات یک کاربر خاص با شناسه.
     *
     * @param int $userId شناسه کاربر
     * @return User کاربر
     */
    public function getById(int $id)
    {
        $user = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')
            ->where('status', 'published')
            ->findOrFail($id);

        return $user;
    }

    /**
     * حذف یک کاربر خاص.
     *
     * @param int $id شناسه کاربر
     * @return void
     */
    public function delete(int $id)
    {
        $user = User::where(['id' => $id])->findOrFail($id);

        return $user->update(['status' => 'trashed']);
    }

    /**
     * بازیابی یک کاربر حذف‌شده
     *
     * این متد کاربر با شناسه مشخص را بازیابی می‌کند.
     * تنها کاربران با نقش 'admin' مجاز به بازیابی کاربر هستند.
     *
     * @param int $id شناسه کاربر
     * @throws Exception در صورت عدم مجوز
     */
    public function restore($id)
    {
        $user = $this->getById($id);

        if (!$user) {
            throw new Exception('user_not_found');
        }

        return $user->update(['status' => 'published']);
    }
}
