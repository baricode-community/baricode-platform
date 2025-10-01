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

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk quote yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk quote berdasarkan kategori
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get random quote jika tidak ada quote hari ini
     */
    public static function getRandomQuote($category = null)
    {
        $query = self::active();

        if ($category) {
            $query->category($category);
        }

        return $query->inRandomOrder()->first();
    }

    /**
     * Get formatted quote text
     */
    public function getFormattedQuoteAttribute()
    {
        $date = Carbon::now()->format('d M Y');
        $description = "Hai, aku robot yang akan mengirimkan quote harian kita\n";
        $quote = '"' . $this->quote_text . '"' . ($this->author ? ' - ' . $this->author : '') . ' (' . $date . ')';
        return $description . $quote;
    }
}
