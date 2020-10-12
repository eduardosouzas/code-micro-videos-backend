<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{   use DatabaseMigrations, TestValidations, TestSaves;
    
    private $video;
    private $sendData;

    protected function setUp(): void {
        parent::setUp();
        $this->video = factory(Video::class)->create();
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }
    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testInvalidationData() {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        }
    public function testInvalidationMax() {
            $data = [
                'title' => str_repeat('a', 256),
            ];
            $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
            $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    
    }

    public function testInvalidationBoolean() {
            $data = [
                'opened' => 'a'
            ];
            $this->assertInvalidationInStoreAction($data, 'boolean');
            $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationInteger() {
        $data = [
            'duration' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField() {
        $data = [
            'year_launched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationRatingField() {
        $data = [
            'rating' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testInvalidationCategoriesIdField() {
        $data = [
            'categories_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }
    public function testInvalidationGenresIdField() {
        $data = [
            'genres_id' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }
    public function testStore() {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        
        $sendData = $this->sendData + ['categories_id' => [$category->id],'genres_id' => [$genre->id]];

        $response = $this->assertStore($sendData, $this->sendData + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
         $this->assertStore(
            $sendData + ['opened' => true], 
            $this->sendData + ['opened' => true] + ['deleted_at' => null]);
    }
    public function testUpdate() {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        
        $sendData = $this->sendData + ['categories_id' => [$category->id],'genres_id' => [$genre->id]];

        $response = $this->assertUpdate($sendData, $this->sendData + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $this->assertUpdate(
            $sendData + ['opened' => true], 
            $this->sendData + ['opened' => true] + ['deleted_at' => null]);

    }
    public function testRollabackStore() {
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);
        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $request = \Mockery::mock(Request::class);    

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());
            
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Video::all());
        }
        
        


    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function model() {
        return Video::class;
    }
    protected function routeStore() {
        return route('videos.store');
    }
    protected function routeUpdate() {
        return route('videos.update', ['video' => $this->video->id]);
    }
}
