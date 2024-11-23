<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class News
 * 
 * مدل مربوط به اخبار که شامل ویژگی‌ها و روابط آن‌ها است.
 */
class News extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'news';

    protected $fillable = [
        'category_id',
        'title',
        'title_second',
        'slug',
        'summary',
        'body',
        'image',
        'tags',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * ویژگی‌هایی که باید در لاگ ثبت شوند.
     *
     * @var array
     */
    protected static $logAttributes = [
        'title',
        'title_second',
        'slug',
        'summary',
        'body',
        'image',
        'tags',
        'status',
    ];

    /**
     * نام لاگ برای فعالیت‌ها.
     *
     * @var string
     */
    protected static $logName = 'news';

    /**
     * تنظیمات لاگ فعالیت‌ها.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'title_second',
                'slug',
                'summary',
                'body',
                'image',
                'tags',
                'status',
            ])
            ->useLogName('news');
    }

    /**
     * متد boot برای ثبت رویدادهای مختلف مدل.
     *
     * این متد به ثبت فعالیت‌ها در زمان ایجاد، به‌روزرسانی و حذف خبر کمک می‌کند.
     */
    public static function boot()
    {
        parent::boot();

        // static::saved(function ($model) {
        //     $userId = Auth::id();

        //     if ($userId) {
        //         try {
        //             activity()
        //                 ->performedOn($model)
        //                 ->causedBy($userId)
        //                 ->log('تغییرات در مدل News');
        //         } catch (\Exception $e) {
        //             Log::error('Error logging activity: ' . $e->getMessage());
        //         }
        //     }
        // });

        static::created(function ($model) {
            // ثبت فعالیت برای ایجاد
            activity()->performedOn($model)->log('خبر جدید ایجاد شد');
        });

        static::updated(function ($model) {
            // ثبت فعالیت برای به‌روزرسانی
            activity()->performedOn($model)->log('خبر به‌روزرسانی شد');
        });

        static::deleted(function ($model) {
            // ثبت فعالیت برای حذف
            activity()->performedOn($model)->log('خبر حذف شد');
        });
    }


    /**
     * کاربری که خبر را ایجاد کرده است.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * کاربری که خبر را به‌روزرسانی کرده است.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * دسته‌بندی مربوط به این خبر.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->select('id', 'title', 'parent_id');
    }
}
