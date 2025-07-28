<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $appends = ['full_path'];

    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function fullPath()
    {
        $category = $this;
        $names = [];

        while ($category) {
            $names[] = $category->name;
            $category = $category->parent;
        }

        return implode(' / ', array_reverse($names));
    }

    public function getFullPathAttribute()
    {
        $names = [];
        $category = $this;
        while ($category) {
            array_unshift($names, $category->name);
            $category = $category->parent;
        }
        return implode(' / ', $names);
    }


}
