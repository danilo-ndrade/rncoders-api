<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostFormRequest;
use App\Http\Requests\UpdatePostFormRequest;
use App\Models\File;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Post model
     *
     * @var \App\Models\Post $post
     */
    protected $post;

    public function __construct(Post $post) {
        $this->post = $post;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-post') && !$request->user()->hasPermissionTo('visualizar-post')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        if ($request->user()->hasPermissionTo('visualizar-todos-os-post')) {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->latest()->paginate(10);
        } else {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->where('user_id', $request->user()->id)
                ->latest()->paginate(10);
        }

        return response()->json($posts);
    }

    /**
     * Display a listing of the resource.
     * @param string $categorySlug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCategory(Request $request, $categorySlug)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-usuarios') && !$request->user()->hasPermissionTo('visualizar-usuarios')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        if ($request->user()->hasPermissionTo('visualizar-todos-os-usuarios')) {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->whereHas('category', function ($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })
                ->latest()->paginate(10);
        } else {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->where('user_id', $request->user()->id)
                ->whereHas('category', function ($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })
                ->latest()->paginate(10);
        }


        return response()->json($posts);
    }

    /**
     * Display a listing of the resource.
     * @param string $tagSlug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTag(Request $request, $tagSlug)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-usuarios') && !$request->user()->hasPermissionTo('visualizar-usuarios')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        if ($request->user()->hasPermissionTo('visualizar-todos-os-usuarios')) {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->whereHas('tags', function ($query) use ($tagSlug) {
                    $query->where('slug', $tagSlug);
                })
                ->latest()->paginate(10);
        } else {
            $posts = $this->post->with(['author', 'category', 'tags'])
                ->where('user_id', $request->user()->id)
                ->whereHas('tags', function ($query) use ($tagSlug) {
                    $query->where('slug', $tagSlug);
                })
                ->latest()->paginate(10);
        }

        return response()->json($posts);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        if ($post instanceof Post) {
            $user = $post->user;

            $post = [
                "id" => $post->id,
                "title" => $post->title,
                "category" => $post->category,
                "author" => [
                    "name" => $user->name,
                    "image" => $user->image,
                    "description" => $user->description,
                ],
                "tags" => $post->tags,
                "slug" => $post->slug,
                "image" => $post->image,
                "views" => $post->views,
                "keywords" => $post->keywords,
                "description" => $post->description,
                "content" => $post->content,
            ];
        }

        return response()->json($post);
    }

    /**
     * Display the post by its slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function slug($slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (is_null($post)) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $post->views += 1;
        $post->save();

        if ($post instanceof Post) {
            $user = $post->user;
            $post = [
                "id" => $post->id,
                "title" => $post->title,
                "category" => $post->category,
                "author" => [
                    "name" => $user->name,
                    "image" => $user->image,
                    "description" => $user->description,],
                "tags" => $post->tags,
                "slug" => $post->slug,
                "image" => $post->image,
                "views" => $post->views,
                "keywords" => $post->keywords,
                "description" => $post->description,
                "content" => $post->content,
            ];
        }

        return response()->json($post);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePostFormRequest $request)
    {
        $this->authorize('create', Post::class);

        try {
            $post = $this->post->create($request->all());

            if ($request->has('tags')) {
                $request->tags()->sync($request->tags);
            }

            if ($request->file && $request->file->isValid()) {
                $file = $request->file;
                $fileName = time().'-'.$file->getClientOriginalName();
                $upload = Storage::disk('spaces')->put('files', $file);

                if ($upload) {
                    $file = new File([
                        'user_id' => $request->user()->id,
                        'name' => $fileName,
                        'url'  => $upload,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                    ]);

                    $post->file()->save($file);
                }
            }

            return response()->json(['post' => $post], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostFormRequest $request, $id)
    {
        $post = $this->post->findOrFail($id);

        $this->authorize('update', $post);

        try {
            $post->update($request->all());

            if ($request->has('tags')) {
                $request->tags()->sync($request->tags);
            }

            if ($request->file && $request->file->isValid()) {
                $file = $request->file;
                $fileName=time().'-'.$file->getClientOriginalName();
                $upload = Storage::disk('spaces')->put('files', $file);

                if ($upload) {
                    $file = new File([
                        'user_id' => $request->user()->id,
                        'name' => $fileName,
                        'url'  => $upload,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                    ]);

                    $post->file()->save($file);
                }
            }

            return response()->json(['post' => $post]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
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
        $post = $this->post->findOrFail($id);
        $this->authorize('delete', $post);

        try {
            $this->post->destroy($id);

            return response()->json([
                'message' => 'Post deletado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
