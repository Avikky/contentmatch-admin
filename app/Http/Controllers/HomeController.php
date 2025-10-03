<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index(): RedirectResponse
    {
        return Redirect::route('admin.dashboard');
    }
}
