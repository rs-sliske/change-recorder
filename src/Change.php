<?php

namespace Sliske\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Change extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $connection = 'mysql_core';

    protected $fillable = [
    	'subject_id',
        'subject_type',
        'event_name',
        'user_id',
        'before',
        'after',
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function subject(){
    	return $this->morphTo();
    }
}