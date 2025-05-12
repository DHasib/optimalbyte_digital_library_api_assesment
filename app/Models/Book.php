<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['bookshelf_id','title','author','published_year'];

    /**
     * Retrieve the bookshelf associated with the book.
     *
     * Establishes a belongs-to relationship between the Book and Bookshelf models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookshelf(): BelongsTo
    {
        return $this->belongsTo(Bookshelf::class);
    }

    /**
     * Retrieve all chapters associated with the book.
     *
     * This method defines a one-to-many relationship between the Book model
     * and the Chapter model, indicating that a single book can have multiple chapters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

}
