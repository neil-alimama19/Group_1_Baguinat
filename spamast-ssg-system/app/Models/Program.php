<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['institute_id', 'code', 'name'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}