<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasUniqueSlug;

    protected string $slugSource = 'name';

    protected $fillable = ['name', 'slug', 'description'];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}