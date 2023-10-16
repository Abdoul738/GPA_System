<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    //public $timestamps=false;
    protected $fillable = [
        'titre_id',
        'user_id',
        'activite_id',
        'date',
        'statut',
        'halfstatut',
        'activite_sup',
    ];
}
