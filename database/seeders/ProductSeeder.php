<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * تشغيل البذار - إدراج 43 كتاب
     */
    public function run(): void
    {
        $books = [
            // 43 Books with English names
            ['name' => 'New Beginnings', 'price' => 49.99, 'stock' => 50],
            ['name' => 'The Journey of Discovery', 'price' => 54.99, 'stock' => 45],
            ['name' => 'Secrets of Success', 'price' => 59.99, 'stock' => 40],
            ['name' => 'The Art of Living', 'price' => 44.99, 'stock' => 55],
            ['name' => 'Creative Thinking', 'price' => 64.99, 'stock' => 35],
            ['name' => 'The Power of Mind', 'price' => 49.99, 'stock' => 48],
            ['name' => 'The Meaning of Life', 'price' => 55.99, 'stock' => 42],
            ['name' => 'Love and Forgiveness', 'price' => 52.99, 'stock' => 50],
            ['name' => 'The Path of Light', 'price' => 60.99, 'stock' => 38],
            ['name' => 'Ancient Wisdom', 'price' => 57.99, 'stock' => 46],

            ['name' => 'Courage and Boldness', 'price' => 48.99, 'stock' => 52],
            ['name' => 'The Road to Change', 'price' => 63.99, 'stock' => 36],
            ['name' => 'Inner Strength', 'price' => 51.99, 'stock' => 49],
            ['name' => 'Hope and Faith', 'price' => 56.99, 'stock' => 44],
            ['name' => 'Conversations of Life', 'price' => 62.99, 'stock' => 39],
            ['name' => 'The World of Dreams', 'price' => 50.99, 'stock' => 51],
            ['name' => 'Freedom and Limits', 'price' => 58.99, 'stock' => 43],
            ['name' => 'The Path to Growth', 'price' => 47.99, 'stock' => 53],
            ['name' => 'The Secret of Happiness', 'price' => 65.99, 'stock' => 34],
            ['name' => 'The Spirit of Leadership', 'price' => 61.99, 'stock' => 40],

            ['name' => 'Human Genius', 'price' => 53.99, 'stock' => 47],
            ['name' => 'The River of Time', 'price' => 59.99, 'stock' => 41],
            ['name' => 'The Towering Castle', 'price' => 49.99, 'stock' => 54],
            ['name' => 'Authentic Voices', 'price' => 66.99, 'stock' => 32],
            ['name' => 'Bridges to Success', 'price' => 55.99, 'stock' => 45],
            ['name' => 'Divine Revelations', 'price' => 62.99, 'stock' => 37],
            ['name' => 'The Epic of Dreams', 'price' => 51.99, 'stock' => 50],
            ['name' => 'The Spark of Creation', 'price' => 58.99, 'stock' => 43],
            ['name' => 'The Voice of Conscience', 'price' => 47.99, 'stock' => 52],
            ['name' => 'Mirror of the Soul', 'price' => 64.99, 'stock' => 35],

            ['name' => 'The Wave of Transformation', 'price' => 56.99, 'stock' => 44],
            ['name' => 'The Star of Hope', 'price' => 60.99, 'stock' => 39],
            ['name' => 'The Fortress of Will', 'price' => 50.99, 'stock' => 51],
            ['name' => 'Songs of Freedom', 'price' => 67.99, 'stock' => 31],
            ['name' => 'Winds of Renewal', 'price' => 54.99, 'stock' => 46],
            ['name' => 'The Compass of Life', 'price' => 63.99, 'stock' => 36],
            ['name' => 'Cloud of Peace', 'price' => 49.99, 'stock' => 55],
            ['name' => 'The Fortress of Safety', 'price' => 61.99, 'stock' => 40],
            ['name' => 'A New Horizon', 'price' => 52.99, 'stock' => 48],
            ['name' => 'The Sun of Tomorrow', 'price' => 68.99, 'stock' => 30],

            ['name' => 'The Palace of Memories', 'price' => 57.99, 'stock' => 42],
            ['name' => 'The Garden of Wisdom', 'price' => 65.99, 'stock' => 34],
            ['name' => 'Thousand and One Tales', 'price' => 69.99, 'stock' => 29],
        ];

        $now = now();

        // Localized default texts per current app locale
        $locale = app()->getLocale();
        $shortTexts = [
            'ar' => 'كتاب رائع يستحق القراءة',
            'en' => 'A wonderful book worth reading',
            'tr' => 'Okumaya değer harika bir kitap',
        ];
        $descTexts = [
            'ar' => 'كتاب فريد من نوعه يقدم رؤى عميقة وحكمة قيمة. يناسب جميع القراء ويوفر تجربة قراءة ممتعة وفائدة كبيرة.',
            'en' => 'A unique book that offers deep insights and valuable wisdom, suitable for all readers and delivering an enjoyable, rewarding experience.',
            'tr' => 'Tüm okurlara hitap eden, derin içgörüler ve değerli bilgelik sunan, keyifli ve verimli bir okuma deneyimi sağlayan benzersiz bir kitap.',
        ];

        $shortDescription = $shortTexts[$locale] ?? $shortTexts['en'];
        $description = $descTexts[$locale] ?? $descTexts['en'];

        foreach ($books as $bookData) {
            DB::table('products')->insert([
                'name' => $bookData['name'],
                'short_description' => $shortDescription,
                'description' => $description,
                'price' => $bookData['price'],
                'image' => null,
                'stock' => $bookData['stock'],
                'is_active' => DB::raw("'true'::boolean"),
                'is_rejected' => DB::raw("'false'::boolean"),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
