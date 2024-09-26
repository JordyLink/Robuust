<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all the authors max 25
        // route for second page: /api/author?page=2
        $authors = Author::paginate(25);
        // return the authors
        return response()->json($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // validate the input 
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            // create a author object
            $author = new Author;
            // set the name for the author
            $author->name = $request->name;
            // save the author object to the database
            $author->save();

            // return the author
            return response()->json($author);
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
    public function show(Author $author)
    {
        // return the author
        return response()->json($author);
    }

    public function authorExistsById($id)
    {
        return Author::find($id) ? true : false;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        try {
            // validate the input 
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            // change the name of the author
            $author->name = $request->name;
            // save the changes in the database
            $author->save();

            // return the author 
            return response()->json($author);
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
    public function destroy(Author $author)
    {
        try {
            // delete the author from the database
            $author->delete();
            // return a message that the author is deleted
            return response()->json([
                "message" => "Author deleted succesfully!"
            ]);
        } catch (\Throwable $th) {
            // if something went wrong send a message back
            return response()->json([
                "message" => "Something went wrong",
            ]);
            
        }

    }
}
