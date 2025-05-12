<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;
    protected $fillable = ['chapter_id','page_number','content'];

    /**
     * Define an inverse one-to-many relationship with the Chapter model.
     *
     * This method establishes that a Page belongs to a Chapter,
     * allowing access to the chapter details from a page instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}

