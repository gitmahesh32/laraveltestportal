<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCatRequest;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent:id,cat_name','children:id,cat_name,cat_parent_id'])->select('id','cat_name','cat_description','cat_parent_id','cat_image','cat_status');
          
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                 $q->where('cat_name', 'like', "%{$search}%");
            });
        }
         // Pagination (customizable)
        $perPage = $request->get('per_page', 5);
        $result = $query->latest()->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Fetch user data',
            'result' => $result
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('cat_image')) {
                 $data['cat_image'] = $request->file('cat_image')->store('categories', 'public');
            }
            $cat = Category::create($data);
            return response()->json($cat);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCatRequest $request, Category $category)
    {
        try {
            $data = $request->validated();
             if ($request->hasFile('cat_image')) {

                // delete old image
                if ($category->cat_image) {
                    \Storage::disk('public')->delete($category->cat_image);
                }

                // store new image
                $data['cat_image'] = $request->file('cat_image')->store('categories', 'public');
            }

            $category->update($data);

            return response()->json([
                'success' => true,
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $cat = Category::find($id);

        if (!$cat) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        
        $this->deleteCategoryWithChildren($cat);

        return response()->json([
            'message' => 'Category Deleted successfully'
        ]);
    }

    /**
     * Private function delete
     */

    private function deleteCategoryWithChildren($category){
            // 1. Delete children first
        foreach ($category->children as $child) {
            $this->deleteCategoryWithChildren($child);
        }

        // 2. Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // 3. Delete category
        $category->delete();
    }

    /**
     * toggle
     */

    public function toggle($id){
        $cat = Category::findOrFail($id);
        $cat->cat_status = !$cat->cat_status;
        $cat->save();
        return response()->json($cat);
    }

    /**
     * Parent Category Load dropdown
     */

    public function parentDropdownCategory() {
      $categories = Category::whereNull('cat_parent_id')
        ->with('childrenRecursive:id,cat_name,cat_parent_id')
        ->get(['id','cat_name','cat_parent_id']);
        return response()->json($categories);
    }
}
