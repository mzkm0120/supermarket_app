<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supermarket extends Model
{
    use HasFactory;

    protected $table = 'supermarkets'; // 既存のテーブル名を指定
    protected $fillable = ['name', 'longitude', 'latitude', 'website'];
}
