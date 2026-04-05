<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $categories = Category::all();
        return view('admin.category_index', compact('categories'));
    }
    public function store(Request $request) {
        Category::create($request->all());
        return redirect()->back()->with('success', 'Thêm danh mục thành công!');
    }
    public function destroy($id)
{
    $category = Category::findOrFail($id);
    $category->delete();

    return redirect()->back()->with('success', 'Đã xóa danh mục thành công!');
}
}