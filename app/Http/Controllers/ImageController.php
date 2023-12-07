<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageFormRequest;
use App\Models\File;
use App\Models\Image;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Image model
     *
     * @var \App\Models\Image $file
     */
    protected $file;

    public function __construct(
        File $file
    ) {
        $this->file = $file;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-arquivos') && !$request->user()->hasPermissionTo('visualizar-arquivos')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }

        if ($request->user()->hasPermissionTo('visualizar-todos-os-arquivos')) {
            $files = $this->file->latest()->paginate(10);
        } else {
            $files = $this->file->where('user_id', $request->user()->id)
                ->latest()->paginate(10);
        }

        return response()->json(['files' => $files], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ImageFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ImageFormRequest $request)
    {
        $this->authorize('create', Image::class);

        try {
            if ($request->file && $request->file->isValid()) {
                $file = $request->file;
                $fileName=time().'-'.$file->getClientOriginalName();
                $upload = Storage::disk('spaces')->put('files', $file);
    
                if ($upload) {
                    $file = [
                        'user_id' => $request->user()->id,
                        'name' => $fileName,
                        'url'  => $upload,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                    ];
    
                    $file = $this->file->create($file);

                    return response()->json(['file' => $file], 201);
                }
            }
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
        $file = $this->file->findOrFail($id);

        $this->authorize('delete', $file);

        try {
            $this->file->destroy($id);

            return response()->json([
                'file' => 'Arquivo deletado com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
