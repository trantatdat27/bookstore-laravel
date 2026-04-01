<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index(Request $request) {
        $query = Category::query();
        
        // Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = $this->sanitizeSearchInput($request->search);
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        $categories = $query->get();
        return view('admin.category_index', compact('categories'));
    }
    public function store(Request $request) {
        // Validate: từ chối nếu có ký tự đặc biệt
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Custom validation rule: chỉ cho phép chữ, số, dấu cách, dấu gạch ngang, dấu chấm
                function ($attribute, $value, $fail) {
                    $value = trim($value);
                    
                    // Kiểm tra tường minh: từ chối các ký tự đặc biệt
                    if (preg_match('/[@#$%^&*()+\=\[\]{};:"\'<>,?\/\\\\|`~!]/u', $value)) {
                        $fail('Tên danh mục chỉ được chứa chữ cái, số, dấu cách, dấu gạch ngang và dấu chấm. Không được chứa ký tự đặc biệt như @, /, #, $, %, v.v.');
                    }
                    
                    // Kiểm tra không được để trống
                    if (empty($value)) {
                        $fail('Tên danh mục không được để trống.');
                    }
                }
            ]
        ]);
        
        // Trim dữ liệu trước khi lưu
        $validated['name'] = trim($validated['name']);
        
        Category::create($validated);
        return redirect()->back()->with('success', 'Thêm danh mục thành công!');
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Đã xóa danh mục thành công!');
    }
    
    /**
     * Sanitize input: loại bỏ ký tự đặc biệt
     * Chỉ cho phép chữ cái (kể cả tiếng Việt), số, dấu cách, dấu gạch ngang, dấu chấm
     */
    private function sanitizeInput($input) {
        // Loại bỏ ký tự đặc biệt: @ / \ # $ % ^ & * ( ) + = [ ] { } ; : " ' < > , ? . | `
        $input = trim($input);
        
        // Cách 1: Loại bỏ tất cả ký tự đặc biệt
        $sanitized = preg_replace('/[^\\p{L}\\p{N}\\s\\-\\.]/u', '', $input);
        
        // Cách 2 (backup): Nếu cách 1 không hoạt động, dùng blacklist
        if (empty($sanitized) && !empty($input)) {
            $sanitized = preg_replace('/[@#$%^&*()+\\=\\[\\]{};:"\'<>,?\\/\\|`~]/u', '', $input);
        }
        
        return trim($sanitized);
    }
    
    /**
     * Sanitize search input: loại bỏ ký tự đặc biệt cho tìm kiếm
     */
    private function sanitizeSearchInput($input) {
        $input = trim($input);
        
        // Cách 1: Loại bỏ tất cả ký tự đặc biệt
        $sanitized = preg_replace('/[^\\p{L}\\p{N}\\s\\-\\.]/u', '', $input);
        
        // Cách 2 (backup): Nếu cách 1 không hoạt động, dùng blacklist
        if (empty($sanitized) && !empty($input)) {
            $sanitized = preg_replace('/[@#$%^&*()+\\=\\[\\]{};:"\'<>,?\\/\\|`~]/u', '', $input);
        }
        
        return trim($sanitized);
    }
}