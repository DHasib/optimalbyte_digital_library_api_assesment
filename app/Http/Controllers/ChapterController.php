<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChapterController extends Controller
{

    /**
     * Display a listing of chapters for the specified book.
     *
     * Retrieves all chapters associated with the provided book ID, orders them
     * by chapter number, and returns the collection as a JSON response.
     *
     * @param int $bookId The unique identifier of the book.
     * @return \Illuminate\Http\JsonResponse JSON response containing a success indicator and the chapters data.
     */
    public function index($bookId)
    {
        // Validate the bookId
        if (!is_numeric($bookId) || $bookId <= 0) {
            return response()->json(['success'=>false,'message'=>'Invalid book ID'],400);
        } else if (Chapter::where('book_id',$bookId)->count() == 0) {
            return response()->json(['success'=>false,'message'=>'There are no chapters in this book'],404);
        }
        $chapters = Chapter::where('book_id', $bookId)
                           ->orderBy('chapter_number')
                           ->get();

        return response()->json(['success' => true, 'data' => $chapters]);
    }


    /**
     * Store a new chapter in the database.
     *
     * This method performs request validation on the input data, ensuring that:
     * - 'title' is required, must be a string, and cannot exceed 255 characters.
     * - 'chapter_number' is required, must be an integer, and must be at least 1.
     * - 'book_id' is required and must exist in the 'books' table.
     *
     * Once validated, a new Chapter record is created with the provided data.
     *
     * @param  \Illuminate\Http\Request  $req  The HTTP request instance containing the input data.
     * @return \Illuminate\Http\JsonResponse       A JSON response with a success indicator and the created chapter data.
     *
     * @throws \Illuminate\Validation\ValidationException If validation of the input data fails.
     */
    public function store(Request $req, $bookId) {
        $data = $req->validate([
            'title'          => 'required|string|max:255',
            'chapter_number' => 'required|integer|min:1',
        ]);

        if (!is_numeric($bookId) || $bookId <= 0) {
            return response()->json(['success'=>false,'message'=>'Invalid book ID'],400);
        } else if (Chapter::where('book_id',$bookId)->count() == 0) {
            return response()->json(['success'=>false,'message'=>'There are no chapters in this book to update'],404);
        }

        $chapter = Chapter::create(array_merge($data, ['book_id' => $bookId]));
        return response()->json(['success'=>true,'data'=>$chapter],201);
    }

    /**
     * Display the specified chapter along with its pages.
     *
     * This method attempts to fetch a chapter by the provided ID, including its related pages data.
     * Upon successful retrieval, a JSON response containing the chapter data is returned.
     * If no chapter is found, it catches the ModelNotFoundException and returns a JSON error response
     * with a 404 status code.
     *
     * @param int $id The unique identifier of the chapter.
     * @return \Illuminate\Http\JsonResponse JSON response containing either the chapter data or an error message.
     */
    public function show($bookId, $id)
    {
        try {
            $ch = Chapter::with('pages')
                         ->where('book_id',$bookId)
                         ->findOrFail($id);

            return response()->json(['success'=>true,'data'=>$ch]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Chapter not found'],404);
        }
    }


    /**
     * Update an existing chapter resource.
     *
     * This method processes an HTTP request to update a chapter. It first validates the input data,
     * ensuring that if provided, 'title' is a string (max 255 characters), 'chapter_number' is an integer (minimum 1),
     * and 'book_id' exists in the books table. The 'id' field is required and must be an integer (minimum 1) corresponding
     * to an existing chapter.
     *
     * After validation, it retrieves the chapter associated with the given 'book_id' and 'id'. If found, the chapter
     * is updated with the provided data. The method returns a JSON response with the updated chapter data on success.
     *
     * If the chapter cannot be found (i.e., the 'book_id' and 'id' do not match any record), a ModelNotFoundException
     * is caught and a JSON response with an error message and a 404 status code is returned.
     *
     * @param \Illuminate\Http\Request $req The incoming HTTP request containing chapter data for validation and update.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing either the updated chapter data or an error message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException When the chapter with the specified 'id' and 'book_id' is not found.
     */
    public function update(Request $req,$bookId,$id)
    {
        try {
            $data = $req->validate([
                'title'          => 'sometimes|required|string|max:255',
                'chapter_number' => 'sometimes|required|integer|min:1',
            ]);
            $ch = Chapter::where('book_id',$bookId)->findOrFail($id);
            $ch->update($data);
            return response()->json(['success'=>true,'data'=>$ch]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Chapter not found'],404);
        }
    }


    /**
     * Delete a chapter identified by its ID.
     *
     * This method retrieves a chapter using the provided ID and, if found, deletes it from the database.
     * Upon successful deletion, it returns a JSON response indicating success. If the chapter is not found,
     * it catches the ModelNotFoundException and returns a JSON response with an appropriate error message
     * and a 404 status code.
     *
     * @param int $id The unique identifier of the chapter to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response confirming the deletion or indicating an error.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Thrown when the chapter with the given ID does not exist.
     */
    public function destroy($bookId, $id)
    {
        try {
            $ch = Chapter::where('book_id', $bookId)->findOrFail($id);
            $ch->delete();
            return response()->json(['success'=>true,'message'=>'Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Chapter not found'],404);
        }
    }


    /**
     * Retrieve a chapter by its ID along with all its pages, and return the chapter data along with the complete concatenated page content.
     *
     * This method fetches the chapter using the provided chapter ID, ensuring that all its related pages are loaded.
     * The pages are then sorted based on their "page_number" and their "content" fields are concatenated with two newline characters as the separator.
     * If the chapter is found, a JSON response is returned containing a flag indicating success, the chapter's basic details, and the complete content.
     * If the chapter is not found, a JSON response is returned indicating failure with an appropriate error message.
     *
     * @param int $chapterId The unique identifier of the chapter to retrieve.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing success status, chapter details, and the concatenated content or an error message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Thrown when the chapter with the given ID does not exist.
     */
    public function fullContent($chapterId)
    {
        try {
            $ch = Chapter::with('pages')->findOrFail($chapterId);
            $full = $ch->pages
                      ->sortBy('page_number')
                      ->pluck('content')
                      ->implode("\n\n");
            return response()->json([
                'success' => true,
                'chapter' => $ch->only(['id','title','chapter_number']),
                'content' => $full
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Chapter not found'],404);
        }
    }
}
