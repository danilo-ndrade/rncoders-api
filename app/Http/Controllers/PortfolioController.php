<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Http\Requests\PortfolioFormRequest;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Portfolio model
     *
     * @var \App\Models\Portfolio $portfolio
     */
    protected $portfolio;

    public function __construct(Portfolio $portfolio) {
        $this->portfolio = $portfolio;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-portfolios') && !$request->user()->hasPermissionTo('visualizar-portfolios')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }
        
        if ($request->user()->hasPermissionTo('visualizar-todas-os-portfolios')) {
            $portfolios = $this->portfolio->paginate(10);
        } else {
            $portfolio = $this->portfolio->where('id', $request->user()->id)->paginate(10);
        }

        return response()->json($portfolios);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Portfolio  $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Portfolio $portfolio)
    {
        $this->authorize('view', $portfolio);

        if ($portfolio instanceof Portfolio) {
            $portfolio = [
                "id" => $portfolio->id,
                "title" => $portfolio->name,
                "image" => $portfolio->image,
                "description" => $portfolio->description,
                "slug" => $portfolio->slug,
            ];
        }

        return response()->json($portfolio);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PortfolioFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PortfolioFormRequest $request)
    {
        $this->authorize('create', Portfolio::class);

        try {
           $portfolio = $this->portfolio->create($request->all());
            
            if ($request->file && $request->file->isValid()) {
                $file = $request->file;
                $fileName = time().'-'.$file->getClientOriginalName();
                $upload = Storage::disk('spaces')->put('files', $file);

                if ($upload) {
                    $file = new File([
                        'name' => $fileName,
                        'url'  => $upload,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                    ]);

                    $portfolio->file()->save($file);
                }
            }              
           
            return response()->json($portfolio, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PortfolioFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PortfolioFormRequest $request, $id)
    {
        $portfolio = $this->portfolio->findOrFail($id);

        $this->authorize('update', $portfolio);

        try {
            $portfolio->update($request->all());
            
            if ($request->file && $request->file->isValid()) {
                $file = $request->file;
                $fileName=time().'-'.$file->getClientOriginalName();
                $upload = Storage::disk('spaces')->put('files', $file);

                if ($upload) {
                    $file = new File([
                        'name' => $fileName,
                        'url'  => $upload,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                    ]);

                    $portfolio->file()->save($file);
                }
            }

            return response()->json($portfolio, 200);
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
        $portfolio = $this->portfolio->findOrFail($id);
        $this->authorize('delete', $portfolio);

        try {
            $this->portfolio->destroy($id);

            return response()->json([
                'message' => 'Portfólio excluído com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
