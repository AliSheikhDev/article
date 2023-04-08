<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Article;
use App\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewArticlesTest extends TestCase
{
   use RefreshDatabase;

    /** @test */
    public function all_articles_can_be_fetched()
    {
        create(Article::class, [], 5);

        $response = $this->getJson(route("api.articles.index"))
                        ->assertStatus(200);

        $this->assertCount(5, $response->json()["data"]);

    }

    /** @test */
    public function articles_are_fetched_by_pagination()
    {
        create(Article::class, [], 20);

        $response = $this->getJson(route("api.articles.index"));

        $this->assertCount(7, $response->json()["data"]);
    }


    /** @test */
    public function articles_can_be_filtered_with_popularity()
    {
        $this->withoutExceptionHandling();

        $popularArticle = create(Article::class, ["visits" => 30]);
        create(Article::class, ["visits" => 0], 2);

        $response = $this->getJson("/api/articles?popular=1")->json();

        $this->assertEquals($popularArticle->slug, $response["data"][0]["slug"]);
    }


    /** @test */
    public function article_can_be_searched()
    {
        $this->withoutExceptionHandling();

        create(Article::class, [], 3);
        $articleToSearch = create(Article::class, ["title" => "Juvenal"]);

        $response = $this->getJson("/api/articles?search=Juvenal")->json();

        $searchedArticlesTitle = collect($response['data'])->pluck("title");

        $this->assertContains($articleToSearch->title, $searchedArticlesTitle);
    }


    /** @test */
    public function we_can_show_a_blog_article()
    {
        $article = create(Article::class);

        $response = $this->getJson(route("articles.show", ["category" => $article->category, "article" => $article]));

        $this->assertContains($article->slug, $response->json()["data"]);
    }


    /** @test */
    public function when_a_article_is_shown_its_visits_count_is_added()
    {
        $article = create(Article::class, ["visits" => 0]);

        $this->getJson(route("articles.show", ["category" => $article->category, "article" => $article]));

        $this->assertEquals(1, $article->fresh()->visits);
    }

    /** @test */
    public function we_can_show_articles_according_to_a_category()
    {
        $category = create(Category::class);

        create(Article::class, ["category_id" => $category->id], 2);

        $response = $this->getJson(route("api.categories.articles", $category));

        $JsonResponse = $response->json();

        $this->assertCount(2, $JsonResponse["data"]);

    }


    /** @test */
    public function we_can_filter_articles_with_tags()
    {
        $taggedArticles = create(Article::class, [], 3);
        create(Article::class, [], 2);

        $tagIds = Tag::add(["php"]);

        foreach ($taggedArticles as $article) {
            $article->tags()->attach($tagIds);
        }

        $tag = Tag::first();

        $response = $this->getJson(route("api.tags.articles",  $tag))->json();

        $this->assertCount(5, Article::all());
        $this->assertCount(3, $response["data"]);

    }


}
