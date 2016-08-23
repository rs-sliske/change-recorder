<?php

use RMoore\ChangeRecorder\Change;

class ChangeRecorderTest extends TestCase
{
    /** @test */
    public function it_can_create_a_change()
    {
        $this->createChange();

        $this->assertEquals(Change::count(), 1);
    }

    /** @test */
    public function it_has_a_subject()
    {
        $post = $this->createPost()->fresh();
        $change = $this->createChange(['subject_id' => 1, 'subject_type' => Post::class]);

        $this->assertEquals($change->subject, $post);
    }

    /** @test */
    public function a_model_has_changes()
    {
        Post::created(function ($post) {
            var_dump('creating post : '.$post->title);
        });
        $post = $this->createPost();

        $this->assertEquals(Change::count(), 1);

        $post->title = 'new title';
        $post->save();

        $this->assertEquals(Change::count(), 2);
    }
}
