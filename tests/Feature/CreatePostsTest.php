<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Article;
use App\Models\Tag;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticlesTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $authToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = create(User::class);
        $this->signIn($this->admin);
        $this->authToken = auth()->tokenById($this->admin->id);
    }


    /** @test */
    public function test_a_blog_can_be_created_by_the_admin()
    {
        $this->withoutExceptionHandling();

        Storage::fake('avatars');

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => true,
            "cover" => UploadedFile::fake()->image('avatar.jpg'),
            "tags" =>"php,java,javascript"
        ];

        $this->articleJson(route("api.articles.store"), $data);

        $this->assertCount(1, Article::all());

        $this->deleteStoreFile(Article::all()->pluck("cover")->toArray());
    }

    /** @test */
    public function test_a_blog_can_be_created_and_linked_to_its_tags()
    {
        $this->withoutExceptionHandling();

        Storage::fake('avatars');

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => true,
            "cover" => UploadedFile::fake()->image('avatar.jpg'),
            "tags" =>"php,java,javascript"
        ];

        $this->articleJson(route("api.articles.store"), $data);

        $article = Article::first();

        $this->assertEquals(3, $article->tags()->count());

        $this->deleteStoreFile(Article::all()->pluck("cover")->toArray());
    }


    /** @test */
    public function a_article_requires_a_title_and_a_content_and_tags()
    {
        Storage::fake('avatars');

        $data = [
            "title" => "",
            "content" => "",
            "category_id" => "",
            "online" => true,
            "cover" => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->articleJson(route("api.articles.store"), $data)->json();

        $this->assertContains("title", array_keys($response["errors"]));
        $this->assertContains("content", array_keys($response["errors"]));
        $this->assertContains("tags", array_keys($response["errors"]));

        $this->deleteStoreFile(Article::all()->pluck("cover")->toArray());
    }

    /** @test */
    public function a_article_requires_a_image_as_cover()
    {
        Storage::fake('avatars');

        $data = [
            "title" => "Lorem",
            "content" => "Some content",
            "category_id" => create(Category::class)->id,
            "online" => true,
            "cover" => ""
        ];

        $this->articleJson(route("api.articles.store"), $data);

        $this->assertCount(0, Article::all());
    }

    /** @test */
    public function a_article_can_be_deleted()
    {
        $article = create(Article::class);

        $this->deleteJson(route("api.articles.destroy", $article));

        $this->assertCount(0, Article::all());
    }

    /** @test */
    public function to_be_online_a_article_depends_on_the_online_parameter()
    {
        $this->withoutExceptionHandling();

        Storage::fake('avatars');

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => false,
            "cover" => UploadedFile::fake()->image('avatar.jpg'),
            "tags" => "php,java"
        ];

        $this->articleJson(route("api.articles.store"), $data);

        $this->assertCount(1, Article::online(false)->get());

        $this->deleteStoreFile(Article::all()->pluck("cover")->toArray());

    }

    /** @test */
    public function a_blog_article_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $article = create(Article::class, ["title" => "Old title", "online" => true]);

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => false,
            "tags" => "php,java"
        ];

        $this->putJson(route("api.articles.update", $article), $data);

        $article = $article->fresh();

        $this->assertEquals($data["title"], $article->title);
        $this->assertEquals($data["online"], !! $article->online);
    }

    /** @test */
    public function blog_article_tags_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $article = create(Article::class, ["title" => "Old title", "online" => true]);
        $tagNames = ["php", "javascript", "java"];
        $tagIds = Tag::add($tagNames);

        $article->tags()->attach($tagIds);

        $this->assertEquals(3, $article->tags()->count());

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => false,
            "tags" => "php,python,elixir,kotlin,elm"
        ];

        $this->putJson(route("api.articles.update", $article), $data);

        $article = $article->fresh();

        $this->assertEquals(5, $article->tags()->count());
    }


    /** @test */
    public function article_cover_images_can_be_updated()
    {
        $article = create(Article::class, ["title" => "Old title", "online" => true]);

        $data = [
            "title" => "Lorem ipsum",
            "content" => $this->faker->paragraph,
            "category_id" => create(Category::class)->id,
            "online" => false,
            "cover" => UploadedFile::fake()->image('avatar.jpg'),
            "tags" => "php,java"
        ];

        $this->putJson(route("api.articles.update", $article), $data);

        $this->assertTrue($article->cover !== $article->fresh()->cover);

        $this->deleteStoreFile(Article::all()->pluck("cover")->toArray());
    }




    private function deleteStoreFile(array $filenames) : void
    {

        if(is_array($filenames)) {
            foreach ($filenames as $filename) {
                Storage::delete("public/covers/{$filename}");
            }
        } else {
            Storage::delete("public/covers/{$filenames}");
        }
    }

}
