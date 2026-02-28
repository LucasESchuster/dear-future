<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'should_notify_at',
        'sender_id',
    ];
}
