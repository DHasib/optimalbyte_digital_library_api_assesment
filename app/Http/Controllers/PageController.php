<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    /**
     * Retrieves and returns pages for a specific chapter, ordered by page number.
     *
     * This method fetches all pages associated with the given chapter ID from the Page model.
     * The results are sorted in ascending order based on the page number.
     *
     * On success, it returns a JSON response with a "success" flag set to true and the data containing the pages.
     * If the chapter is not found, it catches the ModelNotFoundException and returns a JSON response
     * with a "success" flag set to false and an error message, along with a 404 status code.
     *
     * @param int|string $chapterId The ID of the chapter for which pages are being retrieved.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the pages data or an error message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the chapter does not exist.
     */
    public function index($chapterId) {
       try {
            $pages = Page::where('chapter_id',$chapterId)
                     ->orderBy('page_number')
                     ->get();

            return response()->json(['success'=>true,'data'=>$pages]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Chapter not found'],404);
        }
    }

    /**
     * Store a new page within a specified chapter.
     *
     * This method validates the incoming request data to ensure that the 'page_number' and
     * 'content' fields are present and meet the required criteria. Once validated, it creates a
     * new page associated with the given chapter ID, and returns a JSON response including the
     * newly created page data with an HTTP status code of 201.
     *
     * @param \Illuminate\Http\Request $req The HTTP request instance containing the page details.
     * @param int $chapterId The ID of the chapter to which the new page will belong.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing a success flag and the created page data.
     */
    public function store(Request $req, $chapterId) {
        $data = $req->validate([
            'page_number' => 'required|integer|min:1',
            'content'     => 'required|string',
        ]);
        $page = Page::create(array_merge($data, ['chapter_id' => $chapterId]));
        return response()->json(['success'=>true,'data'=>$page],201);
    }

    /**
     * Retrieves and displays a page for a specific chapter.
     *
     * This method finds a page that belongs to the given chapter based on the provided
     * chapter ID and page ID. If the page is found, it returns a JSON response with a success
     * status and the page data. If no matching page is found, it returns a 404 JSON response with
     * an appropriate error message.
     *
     * @param int $chapterId The identifier of the chapter to which the page belongs.
     * @param int $id The unique identifier of the page to retrieve.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the page data if found,
     *                                        or an error message if not found.
     */
    public function show($chapterId, $id) {
        try {
            $pg = Page::where('chapter_id',$chapterId)->findOrFail($id);
            return response()->json(['success'=>true,'data'=>$pg]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }

    /**
     * Updates an existing page associated with a specific chapter.
     *
     * This method validates the provided request data, which may include:
     * - page_number: Must be an integer with a minimum value of 1, if provided.
     * - content: Must be a string, if provided.
     *
     * After validation, it attempts to locate the page resource by its chapter ID and page ID.
     * If the page is found, it updates the page with the validated data and returns a JSON response
     * containing the updated page information.
     *
     * In the event that the page is not found, a JSON response with an error message is returned.
     *
     * @param \Illuminate\Http\Request $req       The HTTP request containing the update data.
     * @param int                     $chapterId The ID of the chapter that the page belongs to.
     * @param int                     $id        The ID of the page to be updated.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response with the outcome of the update operation.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the page with the specified chapter and page ID does not exist.
     */
    public function update(Request $req, $chapterId, $id)
    {
        try {
            $data = $req->validate([
                'page_number' => 'sometimes|required|integer|min:1',
                'content'     => 'sometimes|required|string',
            ]);
            $pg = Page::where('chapter_id',$chapterId)->findOrFail($id);
            $pg->update($data);
            return response()->json(['success'=>true,'data'=>$pg]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }

    /**
     * Deletes a page associated with a specific chapter.
     *
     * This method searches for a page by chapter ID and page ID. If the page exists,
     * it deletes the record and returns a JSON response confirming deletion.
     * If the page is not found, it catches the ModelNotFoundException and returns a JSON error response.
     *
     * @param int|string $chapterId The ID of the chapter the page belongs to.
     * @param int|string $id The ID of the page to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the delete operation.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the page with the specified IDs does not exist.
     */
    public function destroy($chapterId, $id) {
        try {
            $pg = Page::where('chapter_id', $chapterId)->findOrFail($id);
            $pg->delete();
            return response()->json(['success'=>true,'message'=>'Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }
}
