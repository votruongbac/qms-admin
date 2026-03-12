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

}