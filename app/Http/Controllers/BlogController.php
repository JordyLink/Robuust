<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = Blog::with('author');
        // check if the request has a value called created_at and if the value is filled
        // route for created_at /api/blog?created_at=2024-9-26
        if ($request->has('created_at') && $request->created_at != null) {
            // filter the blogs by the created_at date
            $blogs->whereDate('created_at', $request->created_at);
        }

        // check if the request has a value called author_id and if the value is filled
        // route for author_id /api/blog?author_id=2024-9-26
        if ($request->has('author_id') && $request->author_id != null) {
            // filter the blogs by the author
            $blogs->where('author_id', $request->author_id);
        }

        // get all the blogs max 25
        // route for second page: /api/blog?page=2
        $blogs = $blogs->paginate(25);

        // get the amount of created blogs per month in the last 12 months
        $blogs_per_month = Blog::selectRaw('MONTHNAME(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('created_at', 'desc')
            ->get();

        // return the blogs & the blogs per month
        return response()->json([
            'blogs' => $blogs,
            'blogs_per_month' => $blogs_per_month
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // validate the input 
            $request->validate([
                'title' => 'required|string|max:255',
                'image_url' => 'string|max:255',
                'description' => 'required|string|max:1024',
                'author_id' => 'required|numeric'
            ]);

            // check if author exists in database
            if (app(AuthorController::class)->authorExistsById($request->author_id) == false) {
                // if author doesn't exists return message
                return response()->json([
                    "message" => "Author can't be found",
                ]);
            }

            // create a blog object
            $blog = new Blog;
            // set the title for the blog
            $blog->title = $request->title;
            // if there is an image put the url in the database
            $blog->image_url = $request->has('image_url') ? $request->image_url : null;
            // set the description for the blog
            $blog->description = $request->description;
            // add a author by a id in the database
            $blog->author_id = $request->author_id;

            // save the blog object to the database
            $blog->save();

            // return the blog
            return response()->json($blog);
        } catch (\Throwable $th) {
            // if something went wrong send a message back
            return response()->json([
                "message" => "Something went wrong",
            ]);
            
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        $blog->load('author');

        return response()->json($blog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        try {
            // validate the input 
            $request->validate([
                'title' => 'string|max:255',
                'image_url' => 'string|max:255',
                'description' => 'string|max:1024',
                'author_id' => 'numeric'
            ]);

            // check if author exists in database
            if ($request->has('author_id') && $blog->author_id != $request->author_id && app(AuthorController::class)->authorExistsById($request->author_id) == false) {
                // if author doesn't exists return message
                return response()->json([
                    "message" => "Author can't be found",
                ]);
            }

            // if the request has a title then change the title
            if ($request->has('title')) $blog->title = $request->title;
            // if the request has a image_url and there is an image then change the url in the database
            if ($request->has('image_url')) $blog->image_url = $request->image_url;
            // if the request has a description then change the description
            if ($request->has('description')) $blog->description = $request->description;
            // if the request has a author_id then change the author_id
            if ($request->has('author_id')) $blog->author_id = $request->author_id;

            // save the blog object to the database
            $blog->save();

            // return the blog
            return response()->json($blog);
        } catch (\Throwable $th) {
            // if something went wrong send a message back
            return response()->json([
                "message" => "Something went wrong",
            ]);
            
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        try {
            // delete the blog from the database
            $blog->delete();
            // return a message that the blog is deleted
            return response()->json([
                "message" => "Blog deleted succesfully!"
            ]);
        } catch (\Throwable $th) {
            // if something went wrong send a message back
            return response()->json([
                "message" => "Something went wrong",
            ]);
            
        }
    }
}
