<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\ClientFormRequest;


class ClientController extends Controller
{
     /**
     * Client model
     *
     * @var \App\Models\Client $client
     */
    protected $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('visualizar-todos-os-clientes') && !$request->user()->hasPermissionTo('visualizar-clientes')) {
            return response()->json(['error' => 'Not Authorized'], 403);
        }
        
        if ($request->user()->hasPermissionTo('visualizar-todos-os-clientes')) {
            $clients = $this->client->paginate(10);
        } else {
            $clients = $this->client->where('id', $request->user()->id)->paginate(10);
        }

        return response()->json($clients);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Client $client)
    {
        $this->authorize('view', $client);

        if ($client instanceof Client) {
            $client = [
                "id" => $client->id,
                "title" => $client->name,
                "slug" => $client->slug,
            ];
        }

        return response()->json($client);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ClientFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ClientFormRequest $request)
    {
        $this->authorize('create', Client::class);

        try {
            $dataForm = $request->all();
           

            $client = $this->client->create($dataForm);
            return response()->json($client, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ClientFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ClientFormRequest $request, $id)
    {
        $client = $this->client->findOrFail($id);

        $this->authorize('update', $client);

        try {
            $client->update($request->all());

            return response()->json($client, 200);
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
        $client = $this->client->findOrFail($id);
        $this->authorize('delete', $client);

        try {
            $this->client->destroy($id);

            return response()->json([
                'message' => 'Cliente excluído com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
