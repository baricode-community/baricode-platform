<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppGroup extends Model
{
    protected $guarded = ['id'];
    protected $table = 'whatsapp_groups';
    
    protected $fillable = [
        'name',
        'group_id',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this group
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User\User::class, 'created_by');
    }
}
