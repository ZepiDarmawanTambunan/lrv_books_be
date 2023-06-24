<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $categoryResource = CategoryResource::collection($categories);
        return $this->sendResponse($categoryResource, "Successfully get categories");
    }
}
