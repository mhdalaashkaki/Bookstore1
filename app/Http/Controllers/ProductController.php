<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// متحكم المنتجات - إدارة عرض المنتجات في المتجر الأمامي
class ProductController extends Controller
{
    /**
     * عرض قائمة جميع المنتجات في المتجر
     * يعرض 20 منتج في الصفحة مع تقسيمها إلى كروت جميلة
     */
   public function index()
{
    // استخدام true و false بشكل صريح
    $products = Product::query()
        ->whereRaw('is_active IS TRUE')
        ->whereRaw('is_rejected IS FALSE')
        ->paginate(20);

    return view('products.index', compact('products')); // أو اسم الـ View عندك
}

    /**
     * عرض تفاصيل منتج واحد
     * يعرض الشرح الطويل والمفصل للمنتج
     */
    public function show(Product $product): View
    {
        // التحقق من أن المنتج مفعل وغير مرفوض
        if (!$product->is_active || $product->is_rejected) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }

    /**
     * الحصول على بيانات منتج بصيغة JSON
     * تستخدم للطلبات الديناميكية عبر AJAX في صفحة المتجر
     */
    public function getProduct(Product $product)
    {
        // التحقق من أن المنتج مفعل وغير مرفوض
        if (!$product->is_active || $product->is_rejected) {
            return response()->json(['error' => 'المنتج غير متاح'], 404);
        }

        return response()->json($product);
    }

    /**
     * حفظ منتج جديد بطريقة OOP لضمان تحويل القيم البوليانية بشكل صحيح مع Postgres
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'required|image',
        ]);

        // 2. إنشاء كائن جديد مع تحويل صريح للبوليان
        $product = new Product();
        $product->name = $request->name;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->is_active = $request->boolean('is_active');
        $product->is_rejected = $request->boolean('is_rejected');

        // معالجة الصورة
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // 3. الحفظ
        $product->save();

        return redirect()->route('products.index')
                         ->with('success', 'تم إضافة المنتج بنجاح!');
    }
}
