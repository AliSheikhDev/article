<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Tag;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected $article;

    public function setUp(): void
    {
        parent::setUp();

        $this->article = create(Article::class);
    }


    /** @test */
    public function a_article_has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->article->creator);
    }

    /** @test */
    public function a_article_belongs_to_a_category()
    {
        $this->assertInstanceOf(Category::class, $this->article->category);
    }

    /** @test */
    public function we_can_search_articles()
    {
        create(Article::class);
        $article = create(Article::class, ["title" => "Juvenal"]);

        $searchedArticles = Article::search("Juvenal")->get();

        $this->assertContains($article->title, $searchedArticles->pluck("title"));
    }

    /** @test */
    public function it_has_comments()
    {
        $article = create(Article::class);
        create(Comment::class, ["article_id" => $article->id], 2);

        $this->assertCount(2, $article->comments);
    }

    /** @test */
    public function it_can_be_linked_to_tags()
    {
        $tags = create(Tag::class, [],2);
        $article = create(Article::class);

        foreach ($tags as $tag) {
            DB::table("article_tag")->insert([
                "article_id" => $article->id,
                "tag_id"  => $tag->id
            ]);
        }

        $this->assertEquals(2, $article->tags()->count());
    }


}
