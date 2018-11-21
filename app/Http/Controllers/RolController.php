<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = DB::select("SELECT pk_id as codigo, nombre FROM rol;");
        $permisos = DB::select("SELECT r.pk_id as codigo, p.nombre as privilegio FROM rol r, rol_privilegio rp, privilegio as p WHERE r.pk_id= rp.pk_fk_rol AND p.pk_id=rp.pk_fk_privilegio;");
        return view("rol.index", ['roles'=>$roles, 'permisos' => $permisos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permisos = DB::select("SELECT pk_id as codigo, nombre FROM privilegio;");
        return view("rol.create", ['permisos'=>$permisos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validar formulario
        $validateData = $this->validate($request, [
            'nombre' => 'required',
            'permisos' => 'required',
        ]);

        $nombre = $request->input('nombre');
        $privilegios = $request->input('permisos');

        DB::insert("INSERT INTO rol(nombre) VALUES(?);",[$nombre,]);
        $rol_id = DB::select("SELECT currval('rol_sequence') as id;")[0]->id;
        foreach($privilegios as $privilegio){
            DB::insert("INSERT INTO rol_privilegio(pk_fk_rol, pk_fk_privilegio) VALUES (?,?)",[intval($rol_id), intval($privilegio)]);
        }
        return redirect()->action('RolController@index');
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
        $rol = DB::select("SELECT pk_id as codigo, nombre FROM rol WHERE pk_id = ?;", [$id,])[0];
        //$rol_priv = DB::select("SELECT pk_id as codigo, pk_fk_rol as rol, pk_fk_privilegio as privilegio FROM rol_privilegio WHERE pk_fk_rol = ? ;", [$id,]);
        $rol_privs = DB::select("SELECT pk_fk_privilegio as privilegio FROM rol_privilegio WHERE pk_fk_rol = ? ;", [$id,]);
        $rol_priv = array();
        foreach($rol_privs as $r){
            array_push($rol_priv, intval($r->privilegio));
        }

        $permisos = DB::select("SELECT pk_id as codigo, nombre FROM privilegio;");
        return view("rol.create", ['permisos'=>$permisos, 'rol'=>$rol, 'rol_priv'=>$rol_priv]);
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
        // validar formulario
        $validateData = $this->validate($request, [
            'nombre' => 'required',
            'permisos' => 'required',
        ]);

        $nombre = $request->input('nombre');
        $privilegios = $request->input('permisos');

        DB::update("UPDATE rol SET nombre = ? WHERE pk_id=?;",[$nombre,$id]);
        $rol_privs = DB::select("SELECT pk_fk_privilegio as privilegio FROM rol_privilegio WHERE pk_fk_rol = ? ;", [$id,]);
        $rol_priv = array();
        foreach($rol_privs as $r){
            array_push($rol_priv, intval($r->privilegio));
        }

        foreach($privilegios as $r){
            if(!in_array($r, $rol_priv)){
                DB::insert("INSERT INTO rol_privilegio(pk_fk_privilegio, pk_fk_rol) VALUES(?,?);",[$r, $id]);
            }
        }
        foreach($rol_priv as $r){
            if(!in_array($r, $privilegios)){
                DB::delete("DELETE FROM rol_privilegio WHERE pk_fk_privilegio = ? AND pk_fk_rol = ?;",[$r, $id]);
            }
        }
        return redirect()->action('RolController@index');
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
            DB::delete("DELETE FROM rol WHERE pk_id=?;", [$id,]);
            return redirect('admin/rol');
        }catch (QueryException $e) {
            return redirect('admin/rol')->with('status', 'No se puede borrar, tiene asignada claves foraneas con privilegios y/o usuarios');
        }    
    }
}
