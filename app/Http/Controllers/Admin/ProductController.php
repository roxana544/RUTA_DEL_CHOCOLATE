<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChocolateFactory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::with('factory')->orderBy('factory_id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true, 'stock' => 0]),
            'factories' => ChocolateFactory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('admin.products.index')->with('status', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'factories' => ChocolateFactory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        if (\App\Models\OrderItem::where('product_id', $product->id)->exists()) {
            return back()->with('error', 'No se puede eliminar: aparece en pedidos. Desactívalo en su lugar.');
        }
        $product->image && Storage::disk('public')->delete($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Producto eliminado.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'factory_id' => ['required', 'exists:factories,id'],
            'slug' => ['required', 'alpha_dash', 'max:80', Rule::unique('products')->ignore($product)],
            'name_es' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_es' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'string', 'max:40'],
            'cacao' => ['nullable', 'string', 'max:20'],
            'variety' => ['nullable', 'string', 'max:60'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $this->withImage($request, $data, $product, 'products');
    }
}
