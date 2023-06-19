<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory;

    public function jobOffers()
    {
        return $this->hasMany(jobOffers::class);
    }
}
