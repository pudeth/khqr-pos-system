<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Smart search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by category
        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->category_filter);
        }
        
        // Filter by status
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status_filter === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Filter by stock status
        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'low_stock') {
                $query->whereRaw('stock <= min_stock');
            } elseif ($request->stock_filter === 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($request->stock_filter === 'in_stock') {
                $query->where('stock', '>', 0);
            }
        }
        
        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'stock') {
            $query->orderBy('stock', $sortOrder);
        } elseif ($sortBy === 'category') {
            $query->join('categories', 'products.category_id', '=', 'categories.id')
                  ->orderBy('categories.name', $sortOrder)
                  ->select('products.*');
        } else {
            $query->orderBy('name', $sortOrder);
        }
        
        $products = $query->paginate(20)->appends($request->query());
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image_type' => 'required|in:upload,url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        // Handle image
        $imagePath = null;
        if ($request->image_type === 'upload' && $request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('products', 'public');
        } elseif ($request->image_type === 'url' && $request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        $validated['image'] = $imagePath;
        unset($validated['image_type'], $validated['image_file'], $validated['image_url']);

        Product::create($validated);
        return redirect()->back()->with('success', 'Product created successfully');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image_type' => 'required|in:upload,url,keep',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        // Handle image
        if ($request->image_type === 'upload' && $request->hasFile('image_file')) {
            // Delete old image if it's a file upload (not URL)
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image_file')->store('products', 'public');
        } elseif ($request->image_type === 'url' && $request->filled('image_url')) {
            // Delete old image if it's a file upload (not URL)
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->image_url;
        }
        // If 'keep', don't change the image

        unset($validated['image_type'], $validated['image_file'], $validated['image_url']);

        $product->update($validated);
        return redirect()->back()->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $products = Product::with('category')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhereHas('category', function($categoryQuery) use ($query) {
                      $categoryQuery->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get(['id', 'name', 'sku', 'category_id', 'price', 'stock', 'image']);
            
        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->image ? (filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image)) : null
            ];
        }));
    }
}
