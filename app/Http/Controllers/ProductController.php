<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\StoreProduct;
use App\Product;
use App\CategoryParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\cart;
Use Session;


class ProductController extends Controller
{
    public function index()
    {
    	//echo "string";
    	$products = Product::with('categories')->paginate(3);
    	return view('admin.products.index', compact('products'));
    }
    public function trash()
    {
        $products = Product::with('categories')->onlyTrashed()->paginate(3);
        return view('admin.products.index', compact('products'));
    }
    public function create()
    {
        //echo "string";
        $categories = Category::with('childrens')->get();
        return view('admin.products.create', compact('categories'));
    }
    public function show()
    {
    	// echo "string";
        //dd(Session::get('cart'));
       $categories = Category::with('childrens')->get();
       $products = Product::with('categories')->paginate(3);
       return view('products.all', compact('categories','products'));
    }
    public function single(Product $product){
      return view('products.single', compact('product'));
    }
    public function addToCart(Product $product, Request $request){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $qty = $request->qty ? $request->qty : 1;
        $cart = new Cart($oldCart);
        $cart->addProduct($product, $qty);
        Session::put('cart', $cart);
        return back()->with('message', "Product $product->title has been successfully added to Cart");
    }
    public function cart(){

      if(!Session::has('cart')){
        return view('products.cart');
      }
      $cart = Session::get('cart');
      return view('products.cart', compact('cart'));
    }
    public function removeProduct(Product $product){
      $oldCart = Session::has('cart') ? Session::get('cart') : null;
      $cart = new Cart($oldCart);
      $cart->removeProduct($product);
      Session::put('cart', $cart);
      return back()->with('message', "Product $product->title has been successfully removed From the Cart");
   }
   public function updateProduct(Product $product, Request $request){
  
      $oldCart = Session::has('cart') ? Session::get('cart') : null;
      $cart = new Cart($oldCart);
      $cart->updateProduct($product, $request->qty );
      Session::put('cart', $cart);
      return back()->with('message', "Product $product->title has been successfully Updated in the Cart");
   }
   public function store(StoreProduct $request)
    {  
      $path = 'app/public/images/no_thumb.jpeg';
      if($request->has('thumbnail')){
       $extension = ".".$request->thumbnail->getClientOriginalExtension();
       $name = basename($request->thumbnail->getClientOriginalName(), $extension).time();
       $name = $name.$extension;
       $path = $request->thumbnail->storeAs('images', $name, 'public');
     }
       $product = Product::create([
           'title'=>$request->title,
           'slug' => $request->slug,
           'description'=>$request->description,
           'thumbnail' => $path,
           'status' => $request->status,
           'options' => isset($request->extras) ? json_encode($request->extras) : null,
           'featured' => ($request->featured) ? $request->featured : 0,
           'price' => $request->price,
           'discount'=>$request->discount ? $request->discount : 0,
           'discount_price' => ($request->discount_price) ? $request->discount_price : 0,
       ]);
       if($product){
            $product->categories()->attach($request->category_id,['created_at'=>now(), 'updated_at'=>now()]);
            return redirect(route('admin.product.index'))->with('message', 'Product Successfully Added');
       }else{
            return back()->with('message', 'Error Inserting Product');
       }
    }
    public function edit(Product $product)
    {
       $categories = Category::with('childrens')->get();
       return view('admin.products.create',compact('product', 'categories'));
    }
    public function update(StoreProduct $request, Product $product)
    {

        if($request->has('thumbnail')){
            Storage::delete($product->thumbnail);
           $extension = ".".$request->thumbnail->getClientOriginalExtension();
           $name = basename($request->thumbnail->getClientOriginalName(), $extension).time();
           $name = $name.$extension;
           $path = $request->thumbnail->storeAs('images', $name);
           $product->thumbnail = $path;
         }
        $product->title =$request->title;
        //$product->slug = $request->slug;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->featured = ($request->featured) ? $request->featured : 0;
        $product->price = $request->price;
        $product->discount = $request->discount ? $request->discount : 0;
        $product->discount_price = ($request->discount_price) ? $request->discount_price : 0;
        $product->categories()->detach();
        
        if($product->save()){
            $product->categories()->attach($request->category_id, ['created_at'=>now(), 'updated_at'=>now()]);
            return redirect(route('admin.product.index'))->with('message', "Product Successfully Updated!");
        }else{
            return back()->with('message', "Error Updating Product");
        }
    }
    public function recoverProduct($id)
    {
        $product = Product::with('categories')->onlyTrashed()->findOrFail($id);
        if($product->restore())
            return back()->with('message','Product Successfully Restored!');
        else
            return back()->with('message','Error Restoring Product');
    }
    public function destroy(Product $product)
    {
          if($product->categories()->detach() && $product->forceDelete()){
            Storage::delete($product->thumbnail);
            return back()->with('message','Product Successfully Deleted!');
        }else{
            return back()->with('message','Error Deleting Product');
        }
    }
    public function remove(Product $product)
    {
        if($product->delete()){
            return back()->with('message','Product Successfully Trashed!');
        }else{
            return back()->with('message','Error Deleting Product');
        }
    }
}
