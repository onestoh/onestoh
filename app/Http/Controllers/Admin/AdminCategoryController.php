<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller {
    public function index() {
        $categories = AssetCategory::withCount('listings')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:100|unique:asset_categories,name', 'description' => 'nullable|string|max:500', 'icon' => 'nullable|string|max:50']);
        AssetCategory::create($request->only('name','description','icon'));
        return back()->with('success', 'Category created.');
    }
    public function update(Request $request, AssetCategory $category) {
        $request->validate(['name' => 'required|string|max:100|unique:asset_categories,name,'.$category->id, 'description' => 'nullable|string|max:500', 'icon' => 'nullable|string|max:50']);
        $category->update($request->only('name','description','icon'));
        return back()->with('success', 'Category updated.');
    }
    public function destroy(AssetCategory $category) {
        abort_if($category->listings()->exists(), 422, 'Cannot delete category with listings.');
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
