<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTagFormRequest;
use App\Http\Requests\UpdateTagFormRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

class TagController extends Controller
{
    /**
     * Tag model
     *
     * @var \App\Models\Tag $tag
     */
    protected $tag;

    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-as-tags') && !$request->user()->hasPermissionTo('visualizas-tags')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }
        
        if ($request->user()->hasPermissionTo('visualizar-todos-as-tags')) {
            $tags = $this->tag->paginate(10);
        } else {
            $tags = $this->tag->where('user_id', $request->user()->id)->paginate(10);
        }

        return response()->json($tags);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);

        if ($tag instanceof Tag) {
            $tag = [
                "id" => $tag->id,
                "title" => $tag->title,
                "slug" => $tag->slug,
            ];
        }

        return response()->json($tag);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TagFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTagFormRequest $request)
    {
        $this->authorize('create', Tag::class);

        try {
            $dataForm = $request->all();
            $dataForm['user_id'] = $request->user()->id;

            $tag = $this->tag->create($dataForm);
            return response()->json($tag, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TagFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTagFormRequest $request, $id)
    {
        $tag = $this->tag->findOrFail($id);

        $this->authorize('update', $tag);

        try {
            $tag->update($request->all());

            return response()->json($tag, 200);
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
        $tag = $this->tag->findOrFail($id);
        $this->authorize('delete', $tag);

        try {
            $this->tag->destroy($id);

            return response()->json([
                'message' => 'Tag deletada com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
