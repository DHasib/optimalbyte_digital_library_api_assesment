<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookController extends Controller
{


    /**
     * Display a listing of books by bookshelf ID.
     *
     * This method retrieves all books that belong to the specified bookshelf,
     * orders them by their published year in descending order,
     * and returns the result as a JSON response.
     *
     * @param int $shelfId The ID of the bookshelf for which books are retrieved.
     * @return \Illuminate\Http\JsonResponse A JSON response containing a success flag and the list of books.
     */
    public function index($shelfId)
    {
        // Validate the shelfId
        if (!is_numeric($shelfId) || $shelfId <= 0) {
            return response()->json(['success'=>false,'message'=>'Invalid bookshelf ID'],400);
        } else if (Book::where('bookshelf_id',$shelfId)->count() == 0) {
            return response()->json(['success'=>false,'message'=>'There are no books in this bookshelf'],404);
        }
        $books = Book::where('bookshelf_id',$shelfId)
                     ->orderBy('published_year','desc')
                     ->get();
        return response()->json(['success'=>true,'data'=>$books]);
    }


    /**
     * Store a newly created book in the database.
     *
     * This method validates the incoming HTTP request to ensure that the required
     * data for a book is present and formatted correctly. The expected fields include:
     * - title: A required string with a maximum length of 255 characters.
     * - author: A required string with a maximum length of 255 characters.
     * - published_year: A required integer representing the publication year, which must
     *   be between 1000 and one year beyond the current year.
     * - bookshelf_id: A required field that must exist within the bookshelves table (via its id).
     *
     * After successful validation, the book record is created in the database.
     * Finally, the method returns a JSON response with a success flag, the created book data,
     * and an HTTP status code of 201 (Created).
     *
     * @param  \Illuminate\Http\Request  $req The HTTP request instance containing input data.
     * @return \Illuminate\Http\JsonResponse      JSON response containing the success status and created book data.
     */
    public function store(Request $req,$shelfId)
    {
        $data = $req->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'published_year' => 'required|integer|min:1000|max:'.(date('Y')+1),
        ]);
        if (!is_numeric($shelfId) || $shelfId <= 0) {
            return response()->json(['success'=>false,'message'=>'Invalid bookshelf ID'],400);
        } else if (Book::where('bookshelf_id',$shelfId)->count() == 0) {
            return response()->json(['success'=>false,'message'=>'There are no books in this bookshelf to update'],404);
        }
        $book = Book::create(array_merge($data, ['bookshelf_id' => $shelfId]));


        return response()->json([
            'success'=>true,'data'=>$book
        ],201);

    }

    /**
     * Display the specified book along with its chapters.
     *
     * This method retrieves a book with related chapters filtered by the provided bookshelf ID
     * and book ID. If the book is found, it returns a JSON response with the book data; otherwise,
     * it returns a JSON error response indicating that the book was not found.
     *
     * @param int|string $shelfId The identifier of the bookshelf.
     * @param int|string $id The identifier of the book.
     * @return \Illuminate\Http\JsonResponse The JSON response containing book data or an error message.
     */
    public function show($shelfId, $id)
    {
        try {
            $book = Book::with('chapters')
                        ->where('bookshelf_id',$shelfId)
                        ->findOrFail($id);

            return response()->json(['success'=>true,'data'=>$book]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Book not found'],404);
        }
    }

    /**
     * Update a book record.
     *
     * This method validates and updates a book's details. It performs the following operations:
     * - Validates the request data for optional and required fields such as title, author,
     *   published_year, bookshelf_id, and id.
     * - Searches for a book within the specified bookshelf using the provided book id.
     * - Updates the book record with the validated input data.
     * - Returns a JSON response containing the updated book data on success.
     *
     * If the book is not found (i.e., a ModelNotFoundException is thrown), it returns a JSON
     * response with an error message and a 404 status code.
     *
     * @param \Illuminate\Http\Request $req The HTTP request containing the update data.
     *
     * @return \Illuminate\Http\JsonResponse JSON response indicating success/failure and the updated book data or error message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the book specified by id within the given bookshelf cannot be found.
     */
    public function update(Request $req, $shelfId, $id)
    {
        try {
            $data = $req->validate([
                'title'          => 'sometimes|required|string|max:255',
                'author'         => 'sometimes|required|string|max:255',
                'published_year' => 'sometimes|required|integer|min:1000|max:'.(date('Y')+1),
            ]);
            $book = Book::where('bookshelf_id',$shelfId)->findOrFail($id);

            $book->update($data);

            return response()->json(['success'=>true,'data'=>$book]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Book not found'],404);
        }
    }

    /**
     * Delete the specified book from a given bookshelf.
     *
     * This method attempts to locate a book by performing a query on the "books" table
     * filtered by the provided bookshelf ID and then searching for the book by its ID.
     * If the book exists, it is deleted and a JSON response indicating success is returned.
     * If the book is not found, a ModelNotFoundException is caught, and a JSON response
     * indicating failure with a 404 status code is returned.
     *
     * @param int|string $shelfId The ID of the bookshelf containing the book.
     * @param int|string $id The ID of the book to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the delete operation.
     */
    public function destroy($shelfId,$id)
    {
        try {
            $book = Book::where('bookshelf_id', $shelfId)->findOrFail($id);
            $book->delete();
            return response()->json(['success'=>true,'message'=>'Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Book not found'],404);
        }
    }


    /**
     * Searches for books based on a query string.
     *
     * This method validates the incoming request to ensure that a searchable query is provided via
     * the 'query' parameter, which must be a string. It retrieves books whose title or author contains
     * the provided query string using SQL 'LIKE' searches, and returns the results as JSON.
     *
     * @param \Illuminate\Http\Request $req The HTTP request object that includes the search query.
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing the search results.
     */
    public function search(Request $req)
    {
        $req->validate(['query'=>'required|string']);
        $q = $req->input('query');
        $results = Book::where('title','like',"%{$q}%")
                       ->orWhere('author','like',"%{$q}%")
                       ->get();

        return response()->json(['success'=>true,'data'=>$results]);
    }

}

