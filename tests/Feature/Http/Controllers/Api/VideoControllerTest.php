<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
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
            'duration' => 90
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
            'duration' => ''
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
    public function testStore() {
        $response = $this->assertStore($this->sendData, $this->sendData + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $this->assertStore(
            $this->sendData + ['opened' => true], 
            $this->sendData + ['opened' => true] + ['deleted_at' => null]);
    }
    public function testUpdate() {
        $response = $this->assertUpdate($this->sendData, $this->sendData + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $this->assertUpdate(
            $this->sendData + ['opened' => true], 
            $this->sendData + ['opened' => true] + ['deleted_at' => null]);

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
