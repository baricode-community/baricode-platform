<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyekBarengKanboardLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_bareng_id',
        'title',
        'link',
        'description',
    ];

    public function proyekBareng(): BelongsTo
    {
        return $this->belongsTo(ProyekBareng::class, 'proyek_bareng_id', 'id');
    }
}
