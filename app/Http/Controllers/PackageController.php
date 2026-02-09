<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = \App\Models\Package::active()->ordered()->get();
        return view('packages.index', compact('packages'));
    }
}
