<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChocolateFactory;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        return view('admin.stores.index', [
            'stores' => Store::with('factory')->orderBy('factory_id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.stores.form', [
            'store' => new Store(['is_active' => true]),
            'factories' => ChocolateFactory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Store::create($this->validated($request));

        return redirect()->route('admin.stores.index')->with('status', 'Punto de recojo creado.');
    }

    public function edit(Store $store)
    {
        return view('admin.stores.form', [
            'store' => $store,
            'factories' => ChocolateFactory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $store->update($this->validated($request));

        return redirect()->route('admin.stores.index')->with('status', 'Punto de recojo actualizado.');
    }

    public function destroy(Store $store)
    {
        if ($store->orders()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene pedidos. Desactívala en su lugar.');
        }
        $store->delete();

        return redirect()->route('admin.stores.index')->with('status', 'Punto de recojo eliminado.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'factory_id' => ['required', 'exists:factories,id'],
            'name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:200'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'hours' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);
        $data['is_factory_outlet'] = $request->boolean('is_factory_outlet');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
