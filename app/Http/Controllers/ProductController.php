<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();
    
        // 1. Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        // 2. Lọc theo loại sản phẩm
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
    
        // 3. Lọc theo Highlight (Nổi bật)
        if ($request->filled('highlight')) {
            // highlight=1 (Nổi bật), highlight=0 (Thường)
            $query->where('is_highlight', $request->highlight == '1');
        }
    
        return Inertia::render('Products/index', [
            'products' => $query->latest()->paginate(10)->withQueryString(),
            'categories' => Category::all(), // Để đổ vào dropdown lọc
            'filters' => $request->only(['search', 'category', 'highlight']) // Trả lại giá trị để hiển thị trên form
        ]);
    }

    public function store(Request $request)
    {
        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'image_main' => $request->image_main,
            'images_sub' => $request->images_sub, // Lưu dạng JSON
            'specs' => $request->specs, // Lưu dạng JSON
            'description' => $request->description,
            'document' => $request->document,
        ]);

        return redirect()->route('products.index')->with('message', 'Thêm thành công!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Nó sẽ đi tìm file resources/js/Pages/Products/Form.vue
        return inertia('Products/form'); 
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return inertia('Products/show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return inertia('Products/edit', [
            'product' => $product
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Tìm sản phẩm
        $product = Product::findOrFail($id);
    
        // 2. Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image_main' => 'required|string',
            'is_highlight' => 'nullable|boolean', // Thêm trường chúng ta vừa bàn
            'images_sub' => 'nullable|array',
            'specs' => 'nullable|array',
            'description' => 'nullable|string',
            'document' => 'nullable|string',
        ]);
    
        // 3. Cập nhật dữ liệu
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'is_highlight' => $request->boolean('is_highlight'), // Xử lý boolean an toàn
            'image_main' => $request->image_main,
            'images_sub' => $request->images_sub, // Tự động hóa nhờ $casts trong Model
            'specs' => $request->specs,
            'description' => $request->description,
            'document' => $request->document,
        ]);
    
        // 4. Trở về và thông báo
        return redirect()->route('products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
            return redirect()->back()->with('success', 'Đã xóa bài viết thành công!');
    }
    public function indexClient(Request $request)
    {
        $query = Product::query();

        // Chỉ lấy các cột cần thiết cho trang danh sách
        $query->select([
            'id', 
            'name', 
            'price', 
            'image_main', 
            'category', 
            'description'
        ]);
    
        // Lọc theo danh mục (nếu có)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
    
        // Tìm kiếm theo tên (nếu có)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    
        // Trả về dữ liệu phân trang
        $products = $query->latest()->paginate(12);
    
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }
    public function showClient($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ], 200);
    }
}
