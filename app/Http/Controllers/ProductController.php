<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDO;

class ProductController extends Controller
{

    // This will show products page
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->get();
        return view('products.list', ['products' => $products]);
    }

    // This will show create product page
    public function create()
    {
        return view('products.create');
    }

    // This will store a product in DB
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];

        if ($request->image != "") {
            $rules['image'] = 'image';
        }

        $Validator = Validator::make($request->all(), $rules);

        if ($Validator->fails()) {
            return redirect()->route('products.create')->withInput()->withErrors($Validator);
        }

        // Now we will store the product in DB
        $product = new Product();
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();

        if ($request->image != "") {

            // Here we will handle file upload
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;

            // Save image to products directory
            $image->move(public_path('uploads/products'), $imageName);

            // Save Image name in database
            $product->image = $imageName;
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }

    // This will show edit product page
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', [
            'product' => $product
        ]);
    }

    // This will update a product
    public function update($id, Request $request)
    {

        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];

        if ($request->image != "") {
            $rules['image'] = 'image';
        }

        $Validator = Validator::make($request->all(), $rules);

        if ($Validator->fails()) {
            return redirect()->route('products.edit', $product->id)->withInput()->withErrors($Validator);
        }

        // Now we will store the product in DB
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();

        if ($request->image != "") {

            // Delete Old Image
            File::delete(public_path('uploads/products', $product->image));

            // Here we will handle file upload
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;

            // Save image to products directory
            $image->move(public_path('uploads/products'), $imageName);

            // Save Image name in database
            $product->image = $imageName;
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    // This will delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete Image
        // File::delete(public_path('uploads/products', $product->image));
        // Delete Image (check for existence)

        if ($product->image) {
            File::delete(public_path('uploads/products/' . $product->image));
        }

        // Delete Product From DB
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
