<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(3);
        return view('admin.categories.index', compact('categories'));
    }
    public function trash()
    {
        $categories = Category::onlyTrashed()->paginate(3);
        return view('admin.categories.index', compact('categories'));
    }
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create',compact('categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|min:5',
            'slug'=>'required|min:5|unique:categories'
        ]);
        $categories = Category::create($request->only('title','description','slug'));
        $categories->parents()->attach($request->parent_id,['created_at'=>now(), 'updated_at'=>now()]);
        return back()->with('message','Category Added Successfully!');
    }
    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
         $categories = Category::where('id','!=', $category->id)->get();
         return view('admin.categories.create',['categories' => $categories, 'category'=>$category]);
    }
    public function update(Request $request, Category $category)
    {
        $category->title = $request->title;
        $category->description = $request->description;
        $category->slug = $request->slug;
        //detach all parent categories
        $category->parents()->detach();
        //attach selected parent categories
        $category->parents()->attach($request->parent_id,['created_at'=>now(), 'updated_at'=>now()]);
        //save current record into database
        $saved = $category->save();
        //return back to the /add/edit form
        if($saved)
            return back()->with('message','Record Successfully Updated!');
        else
            return back()->with('message', 'Error Updating Category');
    }

    public function recoverCat($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        if($category->restore())
            return back()->with('message','Category Successfully Restored!');
        else
            return back()->with('message','Error Restoring Category');
    }
    public function destroy(Category $category)
    {
        if($category->childrens()->detach() && $category->forceDelete()){
            return back()->with('message','Category Successfully Deleted!');
        }else{
            return back()->with('message','Error Deleting Record');
        }
    }
    public function fetchCategories($id = 0){

        if($id == 0)
            return Category::all();

      $category =  Category::where('id', $id)->first();
      return $category->childrens;
    }
    public function remove(Category $category)
    {
        if($category->delete()){
            return back()->with('message','Category Successfully Trashed!');
        }else{
            return back()->with('message','Error Deleting Record');
        }
    }
}
