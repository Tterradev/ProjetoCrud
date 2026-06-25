<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class User extends Model {

    use HasFactory;

    public function scopeSearch($query, Request $request) {

        if ($request->name) {
            $query->where('name', 'ilike', '%'.$request->name.'%');
        }

        if ($request->email) {
            $query->where('email', 'ilike', '%'.$request->email.'%');
        }    
    }
}