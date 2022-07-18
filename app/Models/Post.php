<?php

namespace App\Models;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, ApiTrait;

    const BORRADOR = 1;
    const PUBLICADO = 2;

    // Relacion uno a muchos inversa
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Relación muchos a muchos
    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    // Relación uno a muchos polimorfica
    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
}
