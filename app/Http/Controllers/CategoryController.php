<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $category_list = Category::withCount('children')->get();
        return view('category.index', compact('category_list'));
    }

    public function store(CategoryRequest $request){
        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->save();
        return back()->with('success', 'تم إضافة البيانات بنجاح');
    }

    public function update(CategoryRequest $request){
        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->save();
        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function delete(Request $request){
        $category = Category::findOrFail($request->id);
        $category->delete();
        return back()->with('success', 'تم حذف البيانات بنجاح');
    }

    public function getSubcategories(Request $request)
    {
        $category_id = $request->category_id;
    
        $subcategories = Category::where('parent_id', $category_id)->get();
    
        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }    

    public function getAllHierarchicalCategories()
    {
        $allCategories = Category::all();
        $final = [];
    
        // احصل على كل الـ parent_ids الموجودة (أي أن لديهم أبناء)
        $parentIds = $allCategories->pluck('parent_id')->filter()->unique();
    
        foreach ($allCategories as $cat) {
            // تجاهل أي تصنيف هو أب (له أبناء)
            if ($parentIds->contains($cat->id)) {
                continue;
            }
    
            // بناء المسار الهرمي للتصنيف النهائي
            $names = [];
            $current = $cat;
            while ($current) {
                $names[] = $current->name;
                $current = $allCategories->firstWhere('id', $current->parent_id);
            }
            $names = array_reverse($names);
    
            $final[] = [
                'id' => $cat->id,
                'full_path' => implode(' / ', $names),
            ];
        }
    
        return response()->json([
            'status' => true,
            'data' => $final
        ]);
    }

}
