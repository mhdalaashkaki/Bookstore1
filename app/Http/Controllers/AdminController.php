<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

// متحكم لوحة الإدارة - إدارة المتجر بشكل كامل
class AdminController extends Controller
{
    /**
     * ميدل وير للتحقق من أن المستخدم أدمن
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            return $next($request);
        });
    }

    /**
     * عرض لوحة تحكم الإدارة الرئيسية
     */
    /**
     * عرض لوحة تحكم الإدارة الرئيسية
     */
    public function dashboard(): View
    {
        $productsCount = Product::count();

        // 🟢 التعديل هنا: استخدام صيغة SQL مباشرة لتجنب تحويل true إلى 1
        $activeProducts = Product::whereRaw('is_active IS TRUE')->count();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $usersCount = User::where('role', 'user')->count();

        $latestOrders = Order::with('user')->where('status', 'pending')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'productsCount',
            'activeProducts',
            'totalOrders',
            'pendingOrders',
            'usersCount',
            'latestOrders'
        ));
    }

    /**
     * عرض قائمة جميع المنتجات للإدارة
     */
    public function products(Request $request): View
    {
        $search = $request->input('q');
        $sort = $request->input('sort', 'id');
        $dir  = $request->input('dir', 'desc');

        // السماح بفرز آمن على أعمدة محددة فقط
        $allowedSorts = ['id', 'name', 'stock', 'price'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        $query = Product::query();

        if ($search) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }

        $products = $query->orderBy($sort, $dir)
                          ->paginate(20)
                          ->appends(['q' => $search, 'sort' => $sort, 'dir' => $dir]);

        return view('admin.products.index', compact('products', 'search', 'sort', 'dir'));
    }

    /**
     * عرض صفحة إضافة منتج جديد
     */
    public function createProduct(): View
    {
        return view('admin.products.create');
    }

    /**
     * حفظ منتج جديد في قاعدة البيانات
     * ✅ تم الإصلاح: التعامل الصحيح مع PostgreSQL Boolean
     */
    public function storeProduct(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'is_rejected' => 'nullable|boolean',
        ]);

        // 2. إنشاء كائن المنتج (الطريقة الآمنة)
        $product = new Product();

        $product->name = $request->name;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        // ✅ إرسال قيم نصية متوافقة مع PostgreSQL ('true'/'false') مع افتراض التفعيل
        $product->is_active = $request->has('is_active') ? 'true' : 'false';
        $product->is_rejected = $request->has('is_rejected') ? 'true' : 'false';

        // رفع الصورة أو استخدام رابط صورة
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
            $product->image_url = null; // في حال وُجدت صورة مرفوعة، نلغي رابط الصورة
        } elseif ($request->filled('image_url')) {
            $product->image_url = $request->input('image_url');
        }

        // 3. الحفظ
        $product->save();

        return redirect()->back()->with('success', 'Product created successfully');
    }

    /**
     * عرض صفحة تعديل منتج
     */
    public function editProduct(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * تحديث بيانات منتج
     */
    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        // التحقق من صحة البيانات
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'is_rejected' => 'nullable|boolean',
        ]);

        // ✅ إرسال قيم نصية متوافقة مع PostgreSQL ('true'/'false') مع افتراض التفعيل
        $data['is_active'] = $request->has('is_active') ? 'true' : 'false';
        $data['is_rejected'] = $request->has('is_rejected') ? 'true' : 'false';

        // معالجة الصورة الجديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
            $data['image_url'] = null;
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->input('image_url');
        }

        // تحديث بيانات المنتج
        $product->update($data);

        return redirect()->route('admin.products')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * حذف منتج
     */
    public function deleteProduct(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * عرض قائمة جميع الطلبات
     */
    public function orders(): View
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * عرض الطلبات المكتملة فقط
     */
    public function completedOrders(): View
    {
        $orders = Order::withTrashed()
            ->with('user')
            ->where('status', 'completed')
            ->latest()
            ->paginate(20);

        return view('admin.orders.completed', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب معين
     */
    public function showOrder(Order $order): View
    {
        $order->load('orderItems.product');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->is_cancelled) {
            return redirect()->back()->with('error', 'لا يمكن تعديل حالة طلب ملغي');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed',
        ]);

        $newStatus = $validated['status'];

        // تأكد من توفر بيانات العناصر والمنتجات
        $order->loadMissing('orderItems.product');

        $shouldDeduct = in_array($newStatus, ['processing', 'shipped', 'completed'], true);

        // منطق خصم المخزون
        if ($shouldDeduct && !$order->stock_deducted) {
            // التحقق أولاً
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                if (!$product || $product->stock < $item->quantity) {
                    $productName = $item->product?->name ?? '#';
                    return redirect()->back()->with('error', "المنتج {$productName} غير متوفر بالكمية المطلوبة");
                }
            }
            // الخصم فعلياً
            foreach ($order->orderItems as $item) {
                $item->product?->decrement('stock', $item->quantity);
            }
            $order->stock_deducted = \Illuminate\Support\Facades\DB::raw("'true'::boolean");
        }

        // استرجاع المخزون في حال العودة لحالة pending
        if ($order->stock_deducted && $newStatus === 'pending') {
            foreach ($order->orderItems as $item) {
                $item->product?->increment('stock', $item->quantity);
            }
            $order->stock_deducted = \Illuminate\Support\Facades\DB::raw("'false'::boolean");
        }

        $order->status = $newStatus;
        $order->save();

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    /**
     * حذف طلب مكتمل (لأدمن فقط)
     */
    public function deleteOrder(Order $order): RedirectResponse
    {
        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'يمكن حذف الطلبات المكتملة فقط');
        }

        $order->delete();
        return redirect()->route('admin.orders.completed')->with('success', 'تم حذف الطلب المكتمل بنجاح');
    }

    /**
     * حذف نهائي لطلب مكتمل
     */
    public function forceDeleteOrder(string $order): RedirectResponse
    {
        $orderModel = Order::withTrashed()->with('orderItems')->findOrFail($order);

        if ($orderModel->status !== 'completed') {
            return redirect()->back()->with('error', 'يمكن حذف الطلبات المكتملة فقط');
        }

        $orderModel->orderItems()->delete();
        $orderModel->forceDelete();

        return redirect()->route('admin.orders.completed')->with('success', 'تم حذف الطلب نهائياً وتم إرسال الثمن إلى المحاسب');
    }

    /**
     * عرض قائمة المستخدمين
     */
    public function users(): View
    {
        $users = User::where('role', 'user')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * رفض بيع المنتج
     */
    public function rejectProduct(Product $product): RedirectResponse
    {
        $product->is_rejected = true;
        $product->save();
        return redirect()->back()->with('success', 'تم تعليم المنتج كمرفوض (غير متوفر)');
    }

    /**
     * استرجاع المنتج
     */
    public function restoreProduct(Product $product): RedirectResponse
    {
        $product->is_rejected = false;
        $product->save();
        return redirect()->back()->with('success', 'تم استرجاع المنتج بنجاح');
    }
}
