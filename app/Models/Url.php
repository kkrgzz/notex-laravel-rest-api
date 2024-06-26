<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = ['note_id', 'url'];

    public function notes()
    {
        return $this->belongsTo(Note::class);
    }
}
