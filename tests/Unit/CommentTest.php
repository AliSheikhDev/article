<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Article;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_comment_belongs_to_a_article()
    {
        $article = create(Article::class);
        $comment = create(Comment::class, ['article_id' => $article->id]);

        $this->assertEquals($article->id, $comment->article->id);
    }
}
