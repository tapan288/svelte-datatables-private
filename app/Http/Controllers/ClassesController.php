<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Http\Resources\ClassesResource;

class ClassesController extends Controller
{
    public function index()
    {
        $classes = Classes::all();

        return ClassesResource::collection($classes);
    }
}
