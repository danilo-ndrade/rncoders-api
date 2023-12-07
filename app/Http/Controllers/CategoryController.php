<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Create\CreateCategoryFormRequest;
use App\Http\Requests\Update\UpdateCategoryFormRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Category model
     *
     * @var \App\Models\Category $category
     */
    protected $category;

    public function __construct(Category $category) {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todas-as-categorias') && !$request->user()->hasPermissionTo('visualizas-categorias')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }
        
        if ($request->user()->hasPermissionTo('visualizar-todas-as-categorias')) {
            $categories = $this->category->paginate(10);
        } else {
            $categories = $this->category->where('user_id', $request->user()->id)->paginate(10);
        }

        return response()->json($categories);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);

        if ($category instanceof Category) {
            $category = [
                "id" => $category->id,
                "title" => $category->title,
                "slug" => $category->slug,
            ];
        }

        return response()->json($category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCategoryFormRequest $request)
    {
        $this->authorize('create', Category::class);

        try {
            $dataForm = $request->all();
            $dataForm['user_id'] = $request->user()->id;

            $category = $this->category->create($dataForm);
            return response()->json($category, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CategoryFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryFormRequest $request, $id)
    {
        $category = $this->category->findOrFail($id);

        $this->authorize('update', $category);

        try {
            $category->update($request->all());

            return response()->json($category, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = $this->category->findOrFail($id);
        $this->authorize('delete', $category);

        try {
            $this->category->destroy($id);

            return response()->json([
                'message' => 'Category deletada com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
