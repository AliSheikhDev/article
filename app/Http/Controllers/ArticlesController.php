<?php

namespace App\Http\Controllers;

use App\Filters\ArticlesFilter;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Category;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{
    protected $filter;



    /**
     * ArticlesController constructor.
     * @param ArticlesFilter $filter
     */
    public function __construct(ArticlesFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Fetch filtered Articles that have an online status
     * @return ArticleCollection
     */
    public function index()
    {
        $filteredArticles  = $this->filter->apply(
            request()->all(),
            Article::online()->latest()
        );

        $articles = $filteredArticles->paginate(7);

        return new ArticleCollection($articles);
    }

    /**
     * Fetch all Articles (online or not)
     */
    public function all()
    {
        return new ArticleCollection(Article::latest()->get());
    }


    /**
     * @param ArticleRequest $request
     * @return ArticleResource
     */
    public function store(ArticleRequest $request)
    {
        $data = $request->data();
        $data["cover_path"] = $this->uploadCover($request->file("cover"));
        $data["visits"] = 0;

        $article = Article::create($data);

        $tagsId = Tag::add(explode(",", $request->tags));
        $article->tags()->attach($tagsId);

        return new ArticleResource($article);
    }

    private function uploadCover(UploadedFile $file) : string
    {
        $filename = time() . "." . $file->getClientOriginalExtension();

        $file->storeAs("public/covers", $filename);

        return asset("storage/covers/". $filename);
    }

    /**
     * @param Category $category
     * @param Article $article
     * @return ArticleResource
     *
     */
    public function show(Category $category, Article $article) : ArticleResource
    {
        if(! auth()->user()) {
            $article->increment("visits");
        }

        $article->load(["category", "creator", "comments"]);

        return new ArticleResource($article);
    }


    /**
     * @param ArticleRequest $request
     * @param Article $article
     * @return ArticleResource
     */
    public function update(ArticleRequest $request, Article $article) : ArticleResource
    {
        $data = $request->data();

        if($request->file("cover")) {
            Storage::delete("public/covers/" . $article->cover);

            $data["cover_path"] = $this->uploadCover($request->file("cover"));
        }

        $article->update($data);

        $tagsId = Tag::add(explode(",", $request->tags));
        $article->tags()->sync($tagsId);

        return new ArticleResource($article);
    }

    /**
     * @param Article $article
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Article $article)
    {
        $article->delete();

        Storage::delete("public/covers/{$article->cover}");

        return response()->json([], 204);
    }
}
