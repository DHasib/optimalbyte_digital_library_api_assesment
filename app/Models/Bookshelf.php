<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bookshelf extends Model {
    use HasFactory;
    protected $fillable = ['name','location'];

    /**
     * Retrieve all books associated with the bookshelf.
     *
     * This method defines a one-to-many relationship between the current model (Bookshelf)
     * and the Book model, allowing you to access all books that belong to a given bookshelf.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}

