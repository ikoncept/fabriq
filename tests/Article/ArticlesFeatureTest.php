<?php

namespace Ikoncept\Fabriq\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class ArticlesFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_store_a_new_article()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('POST', '/api/admin/articles/', [
            'name' => 'En ny nyhet'
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', [
            'name' => 'En ny nyhet',
        ]);
    }

    /** @test **/
    public function it_will_not_create_a_new_article_without_a_title()
    {
        // Arrange

        // Act
        $response = $this->json('POST', '/api/admin/articles/', [
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test **/
    public function it_can_update_an_article()
    {
        // Arrange
        $template = RevisionTemplate::factory()->create([
            'name' => 'Nyhet',
            'slug' => 'article',
            'type' => 'article'
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Titel',
            'key' => 'article_title',
            'translated' => true
        ]);
        $article = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'template_id' => $template->id,
            'name' => 'News item',
            'template_id' => $template->id
        ]);
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/api/admin/articles/' . $article->id, [
            'content' => [
                'article_title' => 'A real title'
            ],
            'name' => 'Nyhet',
            'publishes_at' => '2043-02-02 15:00:00',
            'has_unpublished_time' => true,
            'unpublishes_at' => '2055-02-03 13:00:00'
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'is_published' => false,
            'publishes_at' => '2043-02-02T15:00:00.000000Z'
        ]);

        $this->assertDatabaseHas('articles', [
            'name' => 'Nyhet',
            'publishes_at' => '2043-02-02 15:00:00',
            'has_unpublished_time' => 1,
            'unpublishes_at' => '2055-02-03 13:00:00'
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode('A real title')
        ]);
        $this->assertDatabaseHas('slugs', [
            'slug' => 'a-real-title',
            'locale' => 'sv'
        ]);
    }

    /** @test **/
    public function it_can_get_all_articles()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $articles = \Ikoncept\Fabriq\Models\Article::factory()
            ->count(4)
            ->create();

        // Act
        $response = $this->json('GET', '/api/admin/articles');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(4, 'data');
    }

    /** @test **/
    public function it_can_show_a_single_article()
    {
        // Arrange
        $article = \Ikoncept\Fabriq\Models\Article::factory()->create();

        // Act
        $response = $this->json('GET', '/api/admin/articles/' . $article->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => $article->name
        ]);
    }

    /** @test **/
    public function it_can_search_for_an_article()
    {
        // Arrange
        $article = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'name' => 'Hoola'
        ]);
        $articles = \Ikoncept\Fabriq\Models\Article::factory()
            ->count(3)
            ->create();

        // Act
        $response = $this->json('GET', '/api/admin/articles?filter[search]=hoola');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    /** @test **/
    public function it_can_delete_an_article()
    {
        // Arrange
        $template = RevisionTemplate::factory()
            ->hasFields(1, [
                'key' => 'article_title',
                'type' => 'text',
                'translated' => true
            ])
            ->create([
                'slug' => 'article'
            ]);

        $metaField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => false,
            'key' => 'article_image',
            'type' => 'image'
        ]);
        $article = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'revision' => 1,
            'template_id' => $template->id
        ]);

        $content = [
            'article_title' => 'En titel',
            'article_image' => [1]
        ];
        $article->updateContent($content);

        // Act
        $response = $this->json('DELETE', '/api/admin/articles/' . $article->id);

        // Assert
        // dd(RevisionMeta::all()->toArray());
        $response->assertOk();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id
        ]);
        $this->assertDatabaseMissing('i18n_terms', [
            'key' => 'articles_1_1_article_title'
        ]);
        $this->assertDatabaseMissing('i18n_definitions', [
            'content' => 'En titel'
        ]);
        $this->assertDatabaseMissing('revision_meta', [
            'model_id' => $article->id,
            'model_type' => 'Ikoncept\Fabriq\Models\Article'
        ]);
    }

    /** @test **/
    public function it_can_get_published_articles()
    {
        // Arrange
        $publishedArticle = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'name' => 'Published',
            'publishes_at' => now()->subYear()
        ]);
        $notPublishedYetArticle = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'name' => 'Not published yet',
            'publishes_at' => now()->addYear()
        ]);
        $hasBeenPublished = \Ikoncept\Fabriq\Models\Article::factory()->create([
            'name' => 'Has been published',
            'publishes_at' => now()->subYear(),
            'unpublishes_at' => now()->subMonths(3)
        ]);

        // Act
        $response = $this->json('GET', '/api/admin/articles?filter[published]=1');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }
}
