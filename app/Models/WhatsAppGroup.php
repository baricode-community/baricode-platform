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
     * Get all daily quotes for this group
     */
    public function dailyQuotes()
    {
        return $this->hasMany(DailyQuote::class, 'whatsapp_group_id');
    }
}
