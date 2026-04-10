<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::select('id','product_name','category_id','status','product_image','quantity','price','product_desc');
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                 $q->where('product_name', 'like', "%{$search}%");
            });
        }
        $perPage = $request->get('per_page', 5);
        $result = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Fetch product data',
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
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('product_image')) {
                 $data['product_image'] = $request->file('product_image')->store('products', 'public');
            }
            $cat = Product::create($data);
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
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
             if ($request->hasFile('product_image')) {

                // delete old image
                if ($product->product_image) {
                    \Storage::disk('public')->delete($product->product_image);
                }

                // store new image
                $data['product_image'] = $request->file('product_image')->store('products', 'public');
            }

            $product->update($data);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Pdt = Product::find($id);

        if (!$Pdt) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        Product::destroy($id);

        return response()->json([
            'message' => 'Product Deleted successfully'
        ]);
    }

     /**
     * toggle
     */

    public function toggle($id){
        $pdt = Product::findOrFail($id);
        $pdt->status = !$pdt->status;
        $pdt->save();
        return response()->json($pdt);
    }
}
