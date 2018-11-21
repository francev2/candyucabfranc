<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\QueryException;

class DiarioController extends Controller
{
    public function create(){
        return view('diario.create');
    }

    public function store(Request $request){
        $fechapub = $request->input('fechapub');
        $fechaven = $request->input('fechaven');

        // validar formulario
        $validateData = $this->validate($request, [
            'fechapub' => 'required|date|after_or_equal:'.date('d-m-Y'),
            'fechaven' => 'required|date|after:'.$fechapub,
        ]);

        DB::insert("insert into diariocandy (fech_publi, fech_venc) values (?, ?)", [$fechapub, $fechaven]);
        
        return redirect()->action('DiarioController@index');
    }

    public function index(){
        $diarios = DB::select('select pk_id as codigo, fech_publi as publicado, fech_venc as vence from diariocandy ORDER BY fech_venc DESC;');
        $publicado = DB::select('SELECT d.pk_id AS codigo, p.nombre as nombre, pu.descuento as descuento FROM diariocandy d, producto p, publicado pu WHERE pu.pk_fk_producto = p.pk_id AND d.pk_id = pu.pk_fk_diario ;');
        return view('diario.index', ['diarios' => $diarios, 'publicados' => $publicado]);
    }

    public function getAddProduct($id){
        $diario =  DB::select('select pk_id as codigo, fech_publi as publicado, fech_venc as vence from diariocandy WHERE pk_id = ?;',[$id,]);
        $productos =  DB::select('select pk_id as codigo, nombre as nombre from producto ORDER BY nombre;');
        
        return view('diario.add',['diario' => $diario, 'productos' => $productos]);
    }
    public function getDeleteProduct($id){
        $diario =  DB::select('select pk_id as codigo, fech_publi as publicado, fech_venc as vence from diariocandy WHERE pk_id = ?;',[$id,]);
        $productos =  DB::select('SELECT pu.pk_id as codigo, p.nombre as nombre, pu.descuento as descuento FROM producto p, publicado pu, diariocandy d WHERE pu.pk_fk_producto=p.pk_id AND pu.pk_fk_diario=?;',[$id,]);
        
        return view('diario.delete',['diario' => $diario, 'productos' => $productos]);
    }
    public function postAddProduct(Request $request){
        $descuento = $request->input('descuento');
        $producto = $request->input('producto');
        $diario = $request->input('diariocod');

        // validar formulario
        $validateData = $this->validate($request, [
            'descuento' => 'required|integer',
            'producto' => 'required',
        ]);

        DB::insert("INSERT INTO publicado (pk_fk_diario, pk_fk_producto, descuento) values (?, ?, ?)", [$diario, $producto, $descuento]);
        
        return redirect()->action('DiarioController@index');
    }

    public function postDeleteProduct(Request $request){
        $diario = $request->input('diacod');
        $publicado = $request->input('pubcod');
        DB::delete("DELETE FROM publicado WHERE pk_id=?;", [$publicado,]);
        
        return redirect('admin/diariocandy/diario/'.$diario.'/delete');
    }

    public function edit($diariocandy){
        $diario =  DB::select('select pk_id as codigo, fech_publi as publicado, fech_venc as vence from diariocandy WHERE pk_id = ?;',[$diariocandy,]);
        
        return view('diario.create', ["diario"=>$diario[0]]);
    }

    public function update(Request $request, $diariocandy){
        $fechapub = Input::get('fechapub');
        $fechaven = Input::get('fechaven');

        // validar formulario
        $validateData = $this->validate($request, [
            'fechapub' => 'required|date',
            'fechaven' => 'required|date|after:'.$fechapub,
        ]);

        DB::update("UPDATE diariocandy SET fech_publi = ?, fech_venc =  ? WHERE pk_id=?", [$fechapub, $fechaven,$diariocandy]);
        
        return redirect()->action('DiarioController@index');
    }

    public function deleteDiario(Request $request){

        try {
            $diario = $request->input('codigo');        
            DB::delete("DELETE FROM diariocandy WHERE pk_id=?;", [$diario,]);
            return redirect('admin/diariocandy');
        }catch (QueryException $e) {
            return redirect('admin/diariocandy')->with('status', 'No se puede borrar, tiene asignada claves foraneas');;

        }    
        
    }

}
