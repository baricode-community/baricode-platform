<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProyekBareng extends Model
{
    use HasFactory;

    protected $table = 'proyek_bareng';
    
    protected $primaryKey = 'id';
    
    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'description',
        'is_finished',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = self::generateUniqueId();
            }
        });
    }

    private static function generateUniqueId(): string
    {
        do {
            $id = Str::random(5);
        } while (self::where('id', $id)->exists());
        
        return $id;
    }

    public function meets(): BelongsToMany
    {
        return $this->belongsToMany(Meet::class, 'proyek_bareng_meets', 'proyek_bareng_id', 'meet_id')
                    ->withPivot('description')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'proyek_bareng_users', 'proyek_bareng_id', 'user_id')
                    ->withPivot('description', 'is_approved')
                    ->withTimestamps();
    }

    public function kanboards(): BelongsToMany
    {
        return $this->belongsToMany(Kanboard::class, 'proyek_bareng_kanboards', 'proyek_bareng_id', 'kanboard_id')
                    ->withPivot('description')
                    ->withTimestamps();
    }

    public function kanboardLinks(): HasMany
    {
        return $this->hasMany(ProyekBarengKanboardLink::class, 'proyek_bareng_id', 'id');
    }

    public function polls(): BelongsToMany
    {
        return $this->belongsToMany(Poll::class, 'proyek_bareng_polls', 'proyek_bareng_id', 'poll_id')
                    ->withPivot('title', 'description')
                    ->withTimestamps();
    }
}