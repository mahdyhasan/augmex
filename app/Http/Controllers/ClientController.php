<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function listClients()
    {
        return response()->json(Client::all());
    }

    public function createClient(Request $request)
    {
        $client = Client::create($request->all());
        return response()->json($client, 201);
    }

    public function viewClient($id)
    {
        return response()->json(Client::findOrFail($id));
    }

    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->all());
        return response()->json($client);
    }

    public function deleteClient($id)
    {
        Client::findOrFail($id)->delete();
        return response()->json(['message' => 'Client deleted successfully']);
    }


    
}


