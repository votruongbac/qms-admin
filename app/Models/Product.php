<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * fillable: Cho phép các trường này được lưu vào DB qua hàm create() hoặc update()
     */
    protected $fillable = [
        'name',
        'image_main',
        'images_sub',
        'description',
        'specs',
        'is_highlight',
        'price',
        'category',
        'document'
    ];

    /**
     * casts: Tự động chuyển đổi kiểu dữ liệu khi lấy ra hoặc lưu vào
     * Giúp bạn làm việc với Array trong PHP thay vì phải dùng json_decode/json_encode thủ công.
     */
    protected $casts = [
        'images_sub' => 'json', // Tự động biến JSON thành Mảng khi gọi $product->images_sub
        'specs' => 'json',      // Tự động biến JSON thành Mảng
        'price' => 'float',      // Đảm bảo giá luôn là kiểu số
        'is_highlight' => 'boolean',
    ];
}