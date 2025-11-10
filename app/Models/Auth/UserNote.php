<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNote extends Model
{
    use HasFactory;
    
    protected $guarded = [ 'id' ];
    protected $table = 'user_notes';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
