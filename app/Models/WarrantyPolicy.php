<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyPolicy extends Model
{
    /**
     * Các trường có thể fill từ form
     */
    protected $fillable = [
        'name',
        'duration_days',
        'description',
    ];

    /**
     * Chuyển duration_days thành năm (tính toán)
     */
    public function getYearsAttribute()
    {
        return floor($this->duration_days / 365);
    }

    /**
     * Định dạng mô tả ngắn gọn (nếu cần)
     */
    public function getShortDescriptionAttribute()
    {
        return \Illuminate\Support\Str::limit($this->description, 100);
    }
}