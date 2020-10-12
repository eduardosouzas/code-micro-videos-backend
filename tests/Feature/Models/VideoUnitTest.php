<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        $video = VIdeo::create([
            'title' => 'test_title',
            'description'=> 'test_description',
            'year_launched' => '1979',
            'opened' => true,
            'rating'=> 'L',
            'duration' => 120,
        ]);

        $videos = VIdeo::all();
        $this->assertCount(1, $videos);
        $genreKey = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                 'deleted_at',
                 'created_at', 
                 'updated_at'
            ],
            $genreKey
        );
    }
    public function testCreate() {
        $video = VIdeo::create([
            'title' => 'test_title',
            'description'=> 'test_description',
            'year_launched' => '1979',
            'opened' => true,
            'rating'=> 'L',
            'duration' => 120,
        ]);
        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('test_title', $video->title);
        $this->assertEquals('test_description', $video->description);
        $this->assertEquals('1979', $video->year_launched);
        $this->assertEquals('L', $video->rating);
        $this->assertEquals(120, $video->duration);
        $this->assertTrue($video->opened);

    }
    public function testUpdate() {
        $video = factory(VIdeo::class)->create();
        $data = [
            'title' => 'test_title_changed',
            'description'=> 'test_description_changed',
            'year_launched' => '2020',
            'opened' => false,
            'rating'=> '12',
            'duration' => 140,
        ];

        $video->update($data);
        foreach($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete() {
        $video = factory(VIdeo::class)->create();
        $video->delete();
        $this->assertNull(VIdeo::find($video->id));

        $video->restore();
        $this->assertNotNull(VIdeo::find($video->id));
    }
}
