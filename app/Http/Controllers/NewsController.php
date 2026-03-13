<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    // Hiển thị danh sách tin tức
    public function index()
    {
        $news = News::latest()->paginate(10);
        return Inertia::render('News/index', [
            'news' => $news
        ]);
    }

    // Trang tạo mới
    public function create()
    {
        return Inertia::render('News/create');
    }

    // Lưu tin tức
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|max:255|unique:news,slug',
            'summary'      => 'nullable|string',
            'content'      => 'required|string',
            'thumbnail'    => 'nullable|string', // URL từ API upload gửi về
            'category'     => 'required|string',
            'is_published' => 'boolean',
        ]);
    
        // 1. Nếu có upload file trực tiếp, xử lý và gán vào thumbnail
        if ($request->hasFile('image')) {
            $data['thumbnail'] = $request->file('image')->store('news', 'public');
        }
    
        // 2. ÉP BUỘC: Gán giá trị thumbnail cho trường image
        // Dòng này đảm bảo cả 2 cột trong DB đều có giá trị giống hệt nhau
        $data['image'] = $data['thumbnail'];
    
        // 3. Lưu vào Database
        \App\Models\News::create($data);
    
        return redirect()->route('news.index')->with('message', 'Tạo tin tức thành công!');
    }
    public function show(News $news)
{
    return inertia('News/show', [
        'news' => $news
    ]);
}
public function edit(News $news)
{
    return inertia('News/edit', [
        'news' => $news
    ]);
}
public function update(Request $request, News $news)
{
    // 1. Validate dữ liệu
    $data = $request->validate([
        'title'        => 'required|string|max:255',
        'slug'         => 'required|string|unique:news,slug,' . $news->id,
        'summary'      => 'nullable|string',
        'content'      => 'required|string', // Nơi chứa mã HTML từ Tiptap
        'thumbnail'    => 'nullable|string',
        'category'     => 'required|string',
        'is_published' => 'boolean',
    ]);

    // 2. Cập nhật vào Database
    $news->update($data);

    // 3. Chuyển hướng về trang danh sách hoặc trang chi tiết với thông báo
    return redirect()->route('news.index')->with('success', 'Cập nhật bài viết thành công!');
}
public function destroy(News $news)
    {
        try {
            $news->delete();
            return redirect()->back()->with('success', 'Đã xóa bài viết thành công!');
            
        } catch (\Exception $e) {
            // Trả về lỗi nếu có vấn đề xảy ra (ví dụ lỗi ràng buộc dữ liệu)
            return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể xóa bài viết này.');
        }
    }

}