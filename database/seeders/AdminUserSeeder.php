<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * تشغيل البذار - إنشاء حساب أدمن
     */
    public function run(): void
    {
        // إنشاء حساب أدمن إذا لم يكن موجوداً
        User::firstOrCreate(
            ['email' => 'admin@bookstore.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        echo "✅ تم إنشاء حساب الأدمن بنجاح!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 البريد الإلكتروني: admin@bookstore.com\n";
        echo "👤 اسم المستخدم: admin\n";
        echo "🔑 كلمة المرور: admin123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
