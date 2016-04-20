<?php

namespace MOLiBot\Http\Controllers;

use Illuminate\Http\Request;

use MOLiBot\Http\Requests;
use MOLiBot\Http\Controllers\Controller;

use Storage;

class PMTL extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->cookie('listName')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $files = Storage::files('/');
        $tasks = [];
        foreach ($files as $val) {
            $fname = explode(".", $val);
            if (count($fname) == 3 && $fname[2] == 'json' && $fname[0] == $request->cookie('listName')) {
                $contents = Storage::get($val);
                array_push($tasks, json_decode($contents));
            }
        }

        if (count($tasks) == 0) {
            return response()->json(['message' => 'Nothing Here'], 404);
        }
        
        return response()->json(compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = rand ( 0 , 9999999999 );

        $content = ['id' => $id, 'text' => $request['text'], 'isDone' => (bool)false];

        Storage::put($request->cookie('listName').'.'.$id.'.json', json_encode($content));

        return response()->json(['id' => $id, 'text' => $request['text'], 'isDone' => (bool)false], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$request['text'], $request['isDone']
        if (Storage::disk('local')->has($request->cookie('listName').'.'.$id.'.json')) {
            $oricontents = Storage::get($request->cookie('listName').'.'.$id.'.json');

            $text = json_decode($oricontents, true)['text'];

            Storage::delete($request->cookie('listName').'.'.$id.'.json');

            $content = ['id' => $id, 'text' => $text, 'isDone' => (bool)$request['isDone']];

            Storage::put($request->cookie('listName').'.'.$id.'.json', json_encode($content));

            return response()->json(['id' => $id, 'text' => $text, 'isDone' => (bool)$request['isDone']]);
        }
        return response()->json(['message' => 'Nothing to Update'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (Storage::disk('local')->has($request->cookie('listName').'.'.$id.'.json')) {
            Storage::delete($request->cookie('listName').'.'.$id.'.json');
            return response()->json(['message' => 'Done!! ^_<'], 200);
        }
        return response()->json(['message' => 'Nothing to Delete'], 404);
    }
}
