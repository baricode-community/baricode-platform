<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyQuote extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'daily_quotes';

    protected $fillable = [
        'quote_text',
        'category',
        'whatsapp_group_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the WhatsApp Group that owns this quote
     */
    public function whatsappGroup()
    {
        return $this->belongsTo(WhatsAppGroup::class, 'whatsapp_group_id');
    }

    /**
     * Scope untuk quote yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get random quote jika tidak ada quote hari ini
     */
    public static function getRandomQuote($whatsappGroupId = null)
    {
        $query = self::active();
        
        if ($whatsappGroupId) {
            $query->where('whatsapp_group_id', $whatsappGroupId);
        }
        
        return $query->inRandomOrder()->first();
    }

    /**
     * Get formatted quote text
     */
    public function getFormattedQuoteAttribute()
    {
        $date = Carbon::now()->format('d M Y');
        $groupName = $this->whatsappGroup ? $this->whatsappGroup->name : 'Baricode Community';
        $description = "Hai, aku robot dari {$groupName} yang akan mengirimkan quote harian kita\n";
        $quote = '"' . $this->quote_text . '"'. ' (' . $date . ')';
        return $description . $quote;
    }
}
