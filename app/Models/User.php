<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * مدل مربوط به کاربران که شامل ویژگی‌ها و روابط آن‌ها است.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * ویژگی‌هایی که باید به نوع‌های خاص تبدیل شوند.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * دسته‌بندی‌های ایجاد شده توسط این کاربر.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdCategories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    /**
     * دسته‌بندی‌های به‌روزرسانی شده توسط این کاربر.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedCategories()
    {
        return $this->hasMany(Category::class, 'updated_by');
    }

    /**
     * اخبار ایجاد شده توسط این کاربر.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdNews()
    {
        return $this->hasMany(News::class, 'created_by');
    }

    /**
     * اخبار به‌روزرسانی شده توسط این کاربر.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updatedNews()
    {
        return $this->hasMany(News::class, 'updated_by');
    }
}
