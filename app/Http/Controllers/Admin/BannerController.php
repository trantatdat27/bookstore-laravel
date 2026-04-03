<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = DB::table('banners')->get();
        return view('admin.banners', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg,bmp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Lưu vào thư mục public/uploads/banners
            $file->move(public_path('uploads/banners'), $fileName);
            $path = 'uploads/banners/' . $fileName;

            DB::table('banners')->insert([
                'image' => $path,
                'title' => $request->title,
                'link' => $request->link,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Thêm banner thành công!');
    }

    public function destroy($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        if ($banner) {
            // Xóa file ảnh vật lý trong thư mục
            if (File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }
            DB::table('banners')->where('id', $id)->delete();
        }
        return back()->with('success', 'Đã xóa banner!');
    }
}