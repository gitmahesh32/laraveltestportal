<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class FrontendController extends Controller
{
    //
    public function menu(){
        return Category::whereNull('cat_parent_id')->with('childrenRecursive')->get();
    }

    public function productsByCatId(Request $request)
    {
        // 🔥 Case 1: Product detail
        if ($request->filled('product_id')) {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Product detail',
                'result' => $product
            ]);
        }

        // 🔥 Case 2: Category products
        $query = Product::query()->select(['id','product_name','price','product_image']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

            // Search by name
        if ($request->search) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        if($request->min_price && $request->max_price) {

        }
        // Price filter
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->sort == 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_high') {
            $query->orderBy('price', 'desc');
        }

        $products = $query->simplePaginate(5);

        return response()->json([
            'success' => true,
            'message' => 'Products list',
            'result' => $products
        ]);
    }
}
