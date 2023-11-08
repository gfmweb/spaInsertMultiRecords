<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{

    public function showIndexPage():Illuminate\Contracts\View\Factory
    {
        return view('pages.index');
    }
}
