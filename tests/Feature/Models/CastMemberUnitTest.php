<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CastMemberUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        $castMember = CastMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $castMembers = CastMember::all();
        $this->assertCount(1, $castMembers);
        $castMemberKey = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id', 'name', 'type', 'deleted_at','created_at', 'updated_at'
            ],
            $castMemberKey
        );
    }
    public function testCreate() {
        $castMember = CastMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $castMember->refresh();

        $this->assertEquals(36, strlen($castMember->id));
        $this->assertEquals('test1', $castMember->name);
        $this->assertEquals(CastMember::TYPE_ACTOR, $castMember->type);

        $castMember = castMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $this->assertEquals(CastMember::TYPE_DIRECTOR, $castMember->type);   
    }
    public function testUpdate() {
        $castMember = factory(CastMember::class)->create([
            'name' => 'test_name',
            'type' => CastMember::TYPE_DIRECTOR
        ]);

        $data = [
            'name' => 'test_name_updated',
            'type' => CastMember::TYPE_ACTOR
        ];
        $castMember->update($data);
        foreach($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
        
    }

    public function testDelete() {
        $castMember = factory(CastMember::class)->create();
        $castMember->delete();
        $this->assertNull(CastMember::find($castMember->id));

        $castMember->restore();
        $this->assertNotNull(CastMember::find($castMember->id));
    }
}
