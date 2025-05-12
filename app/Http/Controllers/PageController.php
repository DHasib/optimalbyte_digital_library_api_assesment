<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    public function index($chapterId) {
        $pages = Page::where('chapter_id',$chapterId)
                     ->orderBy('page_number')
                     ->get();

        return response()->json(['success'=>true,'data'=>$pages]);
    }

    public function store(Request $req) {
        $data = $req->validate([
            'page_number' => 'required|integer|min:1',
            'content'     => 'required|string',
            'chapter_id'  => 'required|exists:chapters,id',
        ]);
        $page = Page::create($data);
        return response()->json(['success'=>true,'data'=>$page],201);
    }

    public function show($chapterId, $id) {
        try {
            $pg = Page::where('chapter_id',$chapterId)->findOrFail($id);
            return response()->json(['success'=>true,'data'=>$pg]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }

    public function update(Request $req, $id)
    {
        try {
            $data = $req->validate([
                'page_number' => 'sometimes|required|integer|min:1',
                'content'     => 'sometimes|required|string',
                'chapter_id'  => 'sometimes|required|exists:chapters,id',
            ]);

            $chapterId = $data['chapter_id'];
            $pg = Page::where('chapter_id',$chapterId)->findOrFail($id);
            $pg->update($data);
            return response()->json(['success'=>true,'data'=>$pg]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }

    public function destroy($id) {
        try {
            $pg = Page::findOrFail($id);
            $pg->delete();
            return response()->json(['success'=>true,'message'=>'Deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success'=>false,'message'=>'Page not found'],404);
        }
    }
}
