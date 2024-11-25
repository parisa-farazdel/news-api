<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

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
     * این متد لیستی از کاربران را با توجه به تعداد مورد نظر در هر صفحه 
     * و شماره صفحه ارائه می‌دهد. تنها کاربران با وضعیت 'published' 
     * بازگردانده می‌شوند.
     *
     * @param int $perPage تعداد کاربران در هر صفحه
     * @param int|null $page شماره صفحه
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator محتوای صفحه‌بندی شده
     * 
     * @throws QueryException در صورت بروز خطا در حین دریافت کاربران
     */
    public function getAll(
        int $perPage,
        int $page,
    ) {
        try {
            $users = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')
                ->where('status', 'published')
                ->paginate($perPage, ['*'], 'page', $page);

            return $users;
        } catch (QueryException $e) {
            throw new Exception('error_to_fetch: ' . $e->getMessage());
        }
    }

    /**
     * دریافت اطلاعات یک کاربر خاص با شناسه.
     *
     * این متد اطلاعات کاربر با شناسه مشخص را از پایگاه داده دریافت می‌کند.
     * در صورت عدم وجود کاربر با شناسه مشخص، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $userId شناسه کاربر
     * 
     * @return User کاربر
     * 
     * @throws ModelNotFoundException در صورت عدم وجود کاربر
     */
    public function getById(int $id)
    {
        try {
            $user = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')
                ->where('status', 'published')
                ->findOrFail($id);

            return $user;
        } catch (ModelNotFoundException $e) {
            throw new Exception('not_found_user: ' . $e->getMessage());
        }
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * این متد اطلاعات کاربر جدید را دریافت کرده و آن را در پایگاه داده ذخیره می‌کند.
     *
     * @param string $userName نام کاربری
     * @param string $firstName نام کوچک
     * @param string $lastName نام خانوادگی
     * @param string $email ایمیل
     * @param string $password رمز عبور
     * 
     * @return User کاربر جدید ایجاد شده
     * 
     * @throws ValidationException در صورت بروز خطا در اعتبارسنجی
     * @throws QueryException در صورت بروز خطا در ذخیره‌سازی
     */
    public function store(
        string $userName,
        string $firstName,
        string $lastName,
        string $email,
        string $password,
    ): User {
        try {
            $data = [
                'username' => $userName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make($password),
            ];

            return User::create($data);
        } catch (QueryException $e) {
            throw new Exception('error_saving_user: ' . $e->getMessage());
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر با استفاده از شناسه کاربر.
     *
     * این متد اطلاعات کاربر را بر اساس شناسه آن به‌روزرسانی می‌کند.
     * در صورت عدم وجود کاربر با شناسه مشخص، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $userId شناسه کاربر
     * @param string|null $userName نام کاربری
     * @param string|null $firstName نام کوچک
     * @param string|null $lastName نام خانوادگی
     * @param string|null $email ایمیل
     * @param string|null $password رمز عبور جدید (اختیاری)
     * 
     * @return User اطلاعات به‌روزرسانی شده کاربر
     * 
     * @throws ModelNotFoundException در صورت عدم یافتن کاربر
     * @throws QueryException در صورت بروز خطا در حین به‌روزرسانی
     */
    public function update(
        int $id,
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password = null,
    ) {

        try {
            $user = $this->getById($id);

            if (!$user) {
                throw new ModelNotFoundException('not_found_user');
            }

            $data = [
                'username' => $userName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password ? Hash::make($password) : null
            ];

            $user->update(array_filter($data));

            return $user->fresh();
        } catch (QueryException $e) {
            throw new Exception('error_update_news: ' . $e->getMessage());
        }
    }

    /**
     * حذف یک کاربر خاص.
     *
     * این متد کاربر با شناسه مشخص را از پایگاه داده حذف می‌کند.
     * در صورت عدم وجود کاربر با شناسه مشخص، استثنای ModelNotFoundException پرتاب می‌شود.
     *
     * @param int $id شناسه کاربر
     * @return void
     * @throws ModelNotFoundException در صورت عدم وجود کاربر
     * @throws QueryException در صورت بروز خطا در حین حذف
     */
    public function delete(int $id)
    {
        try {
            $user = User::findOrFail($id);

            if (!$user) {
                throw new ModelNotFoundException('user_not_found');
            }

            return $user->update(['status' => 'trashed']);
        } catch (QueryException $e) {
            throw new Exception('error_delete_user: ' . $e->getMessage());
        }
    }

    /**
     * بازیابی یک کاربر حذف‌شده
     *
     * این متد کاربر با شناسه مشخص را بازیابی می‌کند.
     * تنها کاربران با نقش 'admin' مجاز به بازیابی کاربر هستند.
     *
     * @param int $id شناسه کاربر
     * 
     * @throws ModelNotFoundException در صورت عدم وجود کاربر
     * @throws QueryException در صورت عدم مجوز
     */
    public function restore($id)
    {
        try {
            $user = $this->getById($id);

            if (!$user) {
                throw new ModelNotFoundException('user_not_found');
            }

            $user->update(['status' => 'published']);

            return $user->fresh();
        } catch (QueryException $e) {
            throw new Exception('error_restore_user: ' . $e->getMessage());
        }
    }
}
