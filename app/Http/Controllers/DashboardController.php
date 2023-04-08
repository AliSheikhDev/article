<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $articlesCount = Article::count();
        $commentsCount = Comment::count();
        $viewsCount = Article::sum('visits');

        $popularArticles = Article::with('category')->orderBy('visits', 'DESC')->take(5)->get();
        $latestComments = Comment::with('article')->take(5)->get();

        return response()->json([
            'data' => compact('articlesCount', 'commentsCount', 'viewsCount', 'popularArticles', 'latestComments')
        ]);

    }
}
