<?php

use Faker\Factory as Faker;
use Illuminate\Database\Capsule\Manager as DB;
use RMoore\ChangeRecorder\Change;
use RMoore\ChangeRecorder\RecordsChanges;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public $fake;

    public function setUp()
    {
        $this->fake = Faker::create();
        $this->setUpDatabase();
        $this->migrateTables();
    }

    public function setUpDatabase()
    {
        $database = new DB();

        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->setEventDispatcher(new Dispatcher(new Container));
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    public function migrateTables()
    {
        DB::schema()->create('posts', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });

        DB::schema()->create('changes', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->string('subject_type');
            $table->string('event_name');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function createPost(array $args = [])
    {
        $post = new Post();
        $post->title = array_key_exists('title', $args) ? $args['title'] : $this->fake->sentence;
        $post->content = array_key_exists('content', $args) ? $args['content'] : $this->fake->paragraph;
        $post->save();

        return $post;
    }

    public function createChange(array $args = [])
    {
        return Change::create(array_merge([
            'subject_id'   => 1,
            'subject_type' => Post::class,
            'event_name'   => 'created_post',
            'user_id'      => 1,
            'before'       => [],
            'after'        => [
                'title'   => $this->fake->sentence,
                'content' => $this->fake->paragraph,
            ],
        ], $args));
    }
}


class Post extends \Illuminate\Database\Eloquent\Model
{
    use RecordsChanges;

    protected $fillable = ['title', 'content'];
}
