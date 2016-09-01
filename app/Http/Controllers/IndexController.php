<?php

namespace VKMUSIC\Http\Controllers;

use Illuminate\Http\Request;
use VKMUSIC\Http\Requests;

class IndexController extends Controller
{
    public function getIndex()
    {
        return view('index');
    }
}
