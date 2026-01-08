<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {

        // 1. فحص وإضافة العنوان (إذا لم يكن موجوداً)
        if (!Schema::hasColumn('orders', 'shipping_address')) {
            $table->text('shipping_address')->nullable();
            // ملاحظة: after() اختيارية وقد لا تعمل بدقة في Postgres، لذا يمكن حذفها للأمان
        }

        // 2. فحص وإضافة الهاتف (إذا لم يكن موجوداً)
        if (!Schema::hasColumn('orders', 'phone')) {
            $table->string('phone', 30)->nullable();
        }

        // 3. إضافة الحقول الجديدة (مع الفحص أيضاً للأمان)
        if (!Schema::hasColumn('orders', 'stock_deducted')) {
            $table->boolean('stock_deducted')->default(false);
        }

        if (!Schema::hasColumn('orders', 'is_cancelled')) {
            $table->boolean('is_cancelled')->default(false);
        }

        if (!Schema::hasColumn('orders', 'cancelled_at')) {
            $table->timestamp('cancelled_at')->nullable();
        }
    });
}

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'phone', 'stock_deducted', 'is_cancelled', 'cancelled_at']);
        });
    }
};
