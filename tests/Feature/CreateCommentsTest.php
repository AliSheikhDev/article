<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Article;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
    public function a_visitor_can_create_a_comment()
    {
        $article = create(Article::class);

        $comment = raw(Comment::class, ["content" => "Lorem"]);

        $response = $this->articleJson(route("api.comments.store", ["category" => $article->category, "article" => $article]), $comment);

        $this->assertDatabaseHas("comments", [
            "content" => "Lorem"
        ]);
        $this->assertEquals("Lorem", $response->json()["data"]["content"]);

    }


    /** @test */
    public function to_stored_a_comment_requires_valid_inputs()
    {
        $article = create(Article::class);

        $response = $this->articleJson(route("api.comments.store", ["category" => $article->category, "article" => $article]), []);

        $jsonResponse = $response->json();

        $this->assertTrue(array_key_exists("author_name", $jsonResponse["errors"]));
        $this->assertTrue( array_key_exists("author_email", $jsonResponse["errors"]));
        $this->assertTrue( array_key_exists("content", $jsonResponse["errors"]));

    }

    /** @test */
    public function an_admin_can_see_all_comments()
    {
        $admin = create(User::class);
        $this->signIn($admin);
        $authToken = auth()->tokenById($admin->id);

        create(Comment::class, [],10);

        $response = $this->withHeaders([
                "Bearer" =>  "Authorization {$authToken}"
            ])->getJson(route("api.comments.index"));

        $this->assertCount(10, $response->json()["data"]);
    }


    /** @test */
    public  function an_admin_can_delete_a_comment()
    {
        $this->withoutExceptionHandling();

        $admin = create(User::class);
        $this->signIn($admin);
        $authToken = auth()->tokenById($admin->id);

        $comment = create(Comment::class);

        $this->withHeaders([
            "Bearer" =>  "Authorization {$authToken}"
        ])->deleteJson(route("api.comments.destroy", $comment));

        $this->assertDatabaseMissing("comments", [
            "id"    => $comment->id
        ]);
    }

}
