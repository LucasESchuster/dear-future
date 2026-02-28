<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Model;

class MessageEmail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'message_id',
    ];
}
