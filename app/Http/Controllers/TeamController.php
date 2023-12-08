<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\TeamFormRequest;


class TeamController extends Controller
{
    /**
     * Team model
     *
     * @var \App\Models\Team $team
     */
    protected $team;

    public function __construct(Team $team) {
        $this->team = $team;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
		
        if (!$request->user()->hasPermissionTo('visualizar-todas-as-equipes') && !$request->user()->hasPermissionTo('visualizar-equipes')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }
        
        if ($request->user()->hasPermissionTo('visualizar-todas-as-equipes')) {
            $teams = $this->team->paginate(10);
        } else {
            $team = $this->team->where('id', $request->user()->id)->paginate(10);
        }
        
        return response()->json($teams);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);

        if ($team instanceof Team) {
            $team = [
                "id" => $team->id,
                "title" => $team->name,
                "photo" => $team->photo,
                "description" => $team->description,
                "slug" => $team->slug,
            ];
        }

        return response()->json($team);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TeamFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TeamFormRequest $request)
    {
        $this->authorize('create', Team::class);

        try {
            $team = $this->team->create($request->all());
            
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

                    $team->file()->save($file);
                }
            }              
            
            
            return response()->json($team, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TeamFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TeamFormRequest $request, $id)
    {
        $category = $this->category->findOrFail($id);

        $this->authorize('update', $team);

        try {
            $team->update($request->all());
            
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

                    $team->file()->save($file);
                }
            }

            return response()->json($team, 200);
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
        $team = $this->team->findOrFail($id);
        $this->authorize('delete', $team);

        try {
            $this->team->destroy($id);

            return response()->json([
                'message' => 'Equipe excluída com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
