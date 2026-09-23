<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // 1. Import trait

abstract class Controller
{
    use AuthorizesRequests; // 2. Add trait here
}