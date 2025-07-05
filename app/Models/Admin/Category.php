<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Str;

class Category extends Model
{
  protected $table = 'categories';

    protected $fillable = [
        'name_cate',
        'slug',
        'description',
        'status',

    ];

    /**
     * Tự động tạo slug khi tạo mới
     */
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name_cate);
        });
    }
}
