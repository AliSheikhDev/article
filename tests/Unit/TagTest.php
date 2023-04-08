<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
    public function a_tag_can_be_linked_to_many_articles()
    {
        $tag = create(Tag::class);
        $articles = create(Article::class, [], 2);

        foreach ($articles as $article) {
            DB::table("article_tag")->insert([
                "article_id" => $article->id,
                "tag_id"  => $tag->id
            ]);
        }

        $this->assertEquals(2, $tag->articles()->count());
    }

    /** @test */
    public function many_tags_can_be_added_once()
    {
        $tags = ["php","java","html"];

        Tag::add($tags);

        $this->assertCount(3, Tag::all());
    }

    /** @test */
    public function duplicated_tags_are_added_once()
    {
        $tags = ["php", "java", "html", "php"];

        Tag::add($tags);

        $this->assertCount(3, Tag::all());
    }

    /** @test */
    public  function when_tag_is_added_it_can_not_added_twice()
    {
        Tag::add(["php", "java"]);

        $tags = ["php", "javascript"];

        Tag::add($tags);

        $this->assertCount(3, Tag::all());
    }


    /** @test */
    public  function an_empty_tag_can_not_be_added()
    {
        $tags = ["php","java","html", ""];

        Tag::add($tags);

        $this->assertCount(3, Tag::all());
    }



}
