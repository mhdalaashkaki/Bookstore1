<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // 🟢 هام جداً: استيراد هذا الكلاس
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_description',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
        'is_rejected',
    ];

    /**
     * التحويل الأساسي
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * 🟢 الحل الجذري (Mutators):
     * هذه الدوال تعمل "كالحارس"، أي قيمة تدخل (سواء 1 أو "on" أو true)
     * سيتم تحويلها فوراً إلى true/false قبل أن يفكر لارافيل في فعل أي شيء آخر.
     */

    // تمت إزالة المحولات الخاصة بالقيم المنطقية لتجنب تحويلها إلى أعداد صحيحة مع PostgreSQL

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
