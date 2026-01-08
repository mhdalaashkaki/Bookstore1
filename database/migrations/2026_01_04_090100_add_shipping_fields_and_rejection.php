<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // 1️⃣ أولاً: تعديل جدول الطلبات (orders)
    Schema::table('orders', function (Blueprint $table) {
        // التحقق من العنوان
        if (!Schema::hasColumn('orders', 'shipping_address')) {
            $table->text('shipping_address')->nullable();
        }

        // التحقق من الهاتف
        if (!Schema::hasColumn('orders', 'phone')) {
            $table->string('phone')->nullable();
        }
    }); // <--- 🛑 هنا لازم نغلق جدول الطلبات

    // 2️⃣ ثانياً: تعديل جدول المنتجات (products) بشكل منفصل
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'is_rejected')) {
            // ملاحظة: تأكد أن عمود stock موجود أصلاً، وإلا احذف ->after('stock')
            $table->boolean('is_rejected')->default(false);
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // لا نحذف الأعمدة الموجودة
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_rejected')) {
                $table->dropColumn('is_rejected');
            }
        });
    }
};
