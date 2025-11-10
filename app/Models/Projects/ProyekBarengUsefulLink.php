<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyekBarengUsefulLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_bareng_id',
        'title',
        'description',
        'link',
    ];

    public function proyekBareng(): BelongsTo
    {
        return $this->belongsTo(ProyekBareng::class, 'proyek_bareng_id', 'id');
    }
}