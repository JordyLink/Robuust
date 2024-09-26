<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * Displays the created_at in a nice format
     *
     * @return DateTime 
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:00',
    ];

    /**
     * fillable columns
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image_url',
        'description',
        'author_id',
    ];

    /**
     * Get the author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
