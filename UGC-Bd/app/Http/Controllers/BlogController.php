<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        foreach ($blogs as $blog) {
            $blog->image_url = asset('storage/' . $blog->image);
        }
        return response()->json($blogs, 200);
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
            $data['image'] = $imagePath;
        }

        $blog = Blog::create($data);

        return response()->json($blog, 201);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
            $data['image'] = $imagePath;
        }

        $blog->update($data);

        return response()->json($blog, 200);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog deleted'], 200);
    }


    public function getBlogs()
    {
        $blogs = Blog::all();
        return response()->json($blogs);
    }
    public function show($id)
    {
        $blog = Blog::find($id);
        return view('blog.show', compact('blog'));
    }
}
