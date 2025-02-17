<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SupermarketController extends Controller
{
    public function index()
    {
        return auth()->check() ? view('place') : view('welcome');
    }
}
