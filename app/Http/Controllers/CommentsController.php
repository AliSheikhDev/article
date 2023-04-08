<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentRessource;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;

class CommentsController extends Controller
{

    public function __construct()
    {
        $this->middleware("api")->only(["index"]);
    }

    /**
     * See all the comments resource
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $comments = Comment::latest()->get();

        return response()->json(["data" => $comments]);
    }

    /**
     * Show a comment resource
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
        return response()->json(["data" => $comment], 200);
    }

    /**
     * @param Category $category
     * @param Article $article
     * @return CommentRessource
     */
    public function store(Category $category, Article $article)
    {
        $data  = request()->validate([
            "author_name" => "required|min:1",
            "author_email" => "required|email",
            "content"       => "required|min:2"
        ]);

        $comment = $article->comments()->create($data);

        return new CommentRessource($comment);
    }


    /**
     * Delete a comment resource
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([], 200);
    }
}
