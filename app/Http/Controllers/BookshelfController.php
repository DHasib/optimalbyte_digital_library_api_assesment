<?php

namespace App\Http\Controllers;
use App\Models\Bookshelf;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookshelfController extends Controller
{

    /**
     * Retrieve a sorted list of bookshelves.
     *
     * This method fetches all bookshelf records ordered by their name from the database
     * and returns them in a JSON response with a success flag.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $shelves = Bookshelf::orderBy('name')->get();
        return response()->json(['success'=>true,'data'=>$shelves]);
    }

    /**
     * Store a newly created Bookshelf in storage.
     *
     * This method validates the incoming request to ensure that the 'name' field is provided as a string with a maximum length of 255 characters,
     * and that the 'location' field, if provided, is a string with a maximum length of 255 characters.
     * Once validated, it creates a new Bookshelf record with the provided data and returns a JSON response containing the created record,
     * along with a 201 HTTP status code indicating successful creation.
     *
     * @param Request $req The incoming HTTP request containing shelf data.
     * @return \Illuminate\Http\JsonResponse JSON response with success status and the created bookshelf data.
     */
    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        $shelf = Bookshelf::create($data);
        return response()->json(['success'=>true,'data'=>$shelf],201);
    }

    /**
     * Display the specified bookshelf with its associated books.
     *
     * This method retrieves a bookshelf record by its unique identifier along with the books
     * related to it. On successful retrieval, it returns a JSON response containing the bookshelf data
     * and its associated books. If the bookshelf with the given ID is not found, a JSON error response
     * is returned with a 404 status code.
     *
     * @param int $id The unique identifier of the bookshelf.
     * @return \Illuminate\Http\JsonResponse JSON response with success data or error message.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no bookshelf is found for the given ID.
     */
    public function show($id)
    {
        try {
            $shelf = Bookshelf::with('books')->findOrFail($id);
            return response()->json(['success'=>true,'data'=>$shelf]);
            } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Shelf not found'],404);
            }
    }

    /**
     * Update an existing bookshelf record.
     *
     * This method searches for a bookshelf record by its ID, validates the incoming
     * request data for optional updates to the 'name' and 'location' fields, and then
     * updates the record accordingly. If the bookshelf is not found, it returns a
     * 404 JSON response.
     *
     * @param \Illuminate\Http\Request $req The HTTP request instance containing the update data.
     * @param int|string               $id  The identifier of the bookshelf to update.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the status and the updated data.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no bookshelf record is found for the given ID.
     */
    public function update(Request $req, $id)
    {
        try {
            $shelf = Bookshelf::findOrFail($id);
            $data  = $req->validate([
                'name'     => 'sometimes|required|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);
            $shelf->update($data);
            return response()->json(['success'=>true,'data'=>$shelf]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Shelf not found'],404);
        }
    }

    /**
     * Delete the bookshelf with the specified ID.
     *
     * This method attempts to find a bookshelf record by its ID. If found, it deletes
     * the record and returns a JSON response indicating success. If the record is not found,
     * it catches the ModelNotFoundException and returns a JSON response with an error
     * message and a 404 status code.
     *
     * @param int $id The ID of the bookshelf to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse JSON response indicating the outcome of the deletion.
     */
    public function destroy($id)
    {
        try {
            $shelf = Bookshelf::findOrFail($id);
            $shelf->delete();
            return response()->json(['success'=>true,'message'=>'Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Shelf not found'],404);
        }
    }

}

