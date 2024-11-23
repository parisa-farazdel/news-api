<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 * 
 * مدل مربوط به دسته‌بندی‌ها که شامل ویژگی‌ها و روابط آن‌ها است.
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'id',
        'title',
        'parent_id',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * کاربری که دسته‌بندی را ایجاد کرده است.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * کاربری که دسته‌بندی را به‌روزرسانی کرده است.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * اخبار مربوط به این دسته‌بندی.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany(News::class)
            ->select('id', 'title', 'title_second', 'summary', 'body', 'image', 'tags');
    }
}
