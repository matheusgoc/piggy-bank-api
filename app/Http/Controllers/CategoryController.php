<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryRepository
     */
    private $repo;

    public function __construct(CategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $categories = $this->repo->getAll();
        return CategoryResource::collection($categories);
    }

    public function search($search)
    {
        $categories = $this->repo->search($search);
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
        ]);

        $category = $this->repo->create($request->get('name'));

        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category, $exchange = null)
    {
        $request->validate([
            'exchange' => 'max:50',
        ]);

        $categoryToExchange = $this->repo->delete($category, $exchange);

        return ($categoryToExchange)
            ? new CategoryResource($categoryToExchange)
            : response()->noContent();
    }
}
