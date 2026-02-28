<?php

namespace App\Models\Api\v1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    use HasFactory;
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

      protected function casts(): array
    {
        return [
            'should_notify_at' => 'date',
        ];
    }
}
