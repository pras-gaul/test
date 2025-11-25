<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'code'];

    use HasFactory;

    public function masterItems(): BelongsToMany
    {
        // Mendefinisikan relasi Many-to-Many ke MasterItem
        return $this->belongsToMany(MasterItem::class, 'category_master_item', 'category_id', 'master_item_id');
    }
}
