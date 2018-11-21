<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivilegioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $privilegios = DB::select("SELECT pk_id as codigo, nombre FROM privilegio;");
        return view("privilegio.index", ['privilegios'=>$privilegios]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("privilegio.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nombre = $request->input('nombre');

        // validar formulario
        $validateData = $this->validate($request, [
            'nombre' => 'required',
        ]);

        DB::insert("insert into privilegio (nombre) values (?);", [$nombre,]);
        
        return redirect()->action('PrivilegioController@index');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {        
            DB::delete("DELETE FROM privilegio WHERE pk_id=?;", [$id,]);
            return redirect('admin/privilegio');
        }catch (QueryException $e) {
            return redirect('admin/privilegio')->with('status', 'No se puede borrar, tiene asignada claves foraneas');
        }    
    }
}
