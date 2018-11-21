<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    //
    public function clienteFrecuente(){
        return view('reporte.cliente-frecuente',['data'=>array()]);
    }
    public function postClienteFrecuente(Request $request){
        
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $validateData = $this->validate($request, [
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:'.$desde,
        ]);

        $data = DB::select("SELECT COUNT(j.pk_rif) as cant, j.den_comercial as nombre, t.nombre as tienda
            FROM factura v, cliente_juridico j, tienda t
            WHERE v.tipo='fisica' AND v.fecha_creada BETWEEN ? AND ? AND v.fk_cliente_juridico = j.pk_rif
                    AND v.fk_tienda = t.pk_id
            GROUP BY v.fk_cliente_juridico, j.den_comercial, t.nombre
            UNION
            SELECT COUNT(j.pk_rif) as cant, j.nombre1 as nombre, t.nombre as tienda
            FROM factura v, cliente_natural j, tienda t
            WHERE v.tipo='fisica' AND v.fecha_creada BETWEEN ? AND ? AND v.fk_cliente_natural = j.pk_rif
                    AND v.fk_tienda = t.pk_id
            GROUP BY v.fk_cliente_natural, j.nombre1, t.nombre
            ORDER BY cant desc
            LIMIT 10;"
            ,[$desde, $hasta, $desde, $hasta]);

        return view('reporte.cliente-frecuente',['data'=>$data]);
    }



    public function mejorCliente(){
        return view('reporte.mejor-cliente',['data'=>array()]);
    }
    public function postMejorCliente(Request $request){
        
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $validateData = $this->validate($request, [
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:'.$desde,
        ]);

        $data = DB::select("SELECT SUM(c.suma) as cant, c.rif as rif
            FROM (SELECT SUM(d.precio*d.cantidad) as suma , j.pk_rif as rif, v.pk_id as id
                    FROM factura v, detalle d, cliente_juridico j
                    WHERE d.pk_fk_factura =v.pk_id AND j.pk_rif=v.fk_cliente_juridico 
                            AND v.fecha_creada BETWEEN ? AND ?
                    GROUP BY j.pk_rif, v.pk_id) as c
            GROUP BY c.rif 
            UNION
            SELECT SUM(c.suma) as cant, c.rif as rif
            FROM (SELECT SUM(d.precio*d.cantidad) as suma , j.pk_rif as rif, v.pk_id as id
                    FROM factura v, detalle d, cliente_natural j
                    WHERE d.pk_fk_factura =v.pk_id AND j.pk_rif=v.fk_cliente_natural 
                            AND v.fecha_creada BETWEEN ? AND ?
                    GROUP BY j.pk_rif, v.pk_id) as c
            GROUP BY c.rif 
            ORDER BY cant desc
            LIMIT 5;"
            ,[$desde, $hasta, $desde, $hasta]);

        return view('reporte.mejor-cliente',['data'=>$data]);
    }




    public function presupuesto(){
        $tiendas = DB::select("SELECT pk_id as codigo, nombre
            FROM tienda ;");

        return view('reporte.presupuesto-efectivo',['data'=>array(), 'tiendas'=>$tiendas]);
    }
    public function postPresupuesto(Request $request){
        
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $tienda = $request->input('tienda');

        $validateData = $this->validate($request, [
            'tienda' => 'required|integer',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:'.$desde,
        ]);

        $data = DB::select("SELECT j.pk_rif as rif , t.nombre as nombre
            FROM cliente_juridico j, tienda t, (SELECT DISTINCT f.fk_cliente_juridico as juridico, f.fk_tienda as tienda
                                                FROM factura f
                                                WHERE f.estado=false AND f.tipo='online' AND f.fk_cliente_juridico is not null
                                                        AND f.fecha_creada BETWEEN ? AND ?) as c
            WHERE pk_rif = c.juridico AND ? = t.pk_id AND t.pk_id=c.tienda
            UNION
            SELECT n.pk_rif, t.nombre
            FROM cliente_natural n, tienda t, (SELECT DISTINCT f.fk_cliente_natural as juridico, f.fk_tienda as tienda
                                                FROM factura f
                                                WHERE f.estado=false AND f.tipo='online' AND f.fk_cliente_natural is not null
                                                        AND f.fecha_creada BETWEEN ? AND ?) as c
            WHERE pk_rif = c.juridico AND ? = t.pk_id AND t.pk_id=c.tienda;"
            ,[$desde, $hasta, $tienda, $desde, $hasta, $tienda]);

        
        $tiendas = DB::select("SELECT pk_id as codigo, nombre
        FROM tienda ;");

        return view('reporte.presupuesto-efectivo',['data'=>$data, 'tiendas'=>$tiendas]);
    }

    




    public function masVendido(){
        $tiendas = DB::select("SELECT pk_id as codigo, nombre
            FROM tienda ;");

        return view('reporte.mas-vendido',['data'=>array(), 'tiendas'=>$tiendas]);
    }
    public function postMasVendido(Request $request){
        
        $tienda = $request->input('tienda');

        $validateData = $this->validate($request, [
            'tienda' => 'required|integer',
        ]);

        $data = DB::select("SELECT SUM(d.cantidad) as can, p.nombre as nombre 
            FROM producto p, detalle d, factura f, tienda t
            WHERE d.pk_fk_producto = p.pk_id AND f.pk_id = d.pk_fk_factura
                    AND t.pk_id=? AND t.pk_id=f.fk_tienda AND f.estado=false
            GROUP BY p.nombre;"
            ,[$tienda]);

            
        $tiendas = DB::select("SELECT pk_id as codigo, nombre
        FROM tienda ;");
        return view('reporte.mas-vendido',['data'=>$data, 'tiendas'=>$tiendas]);
    }


    public function puntosClientes(){
        
        $data = DB::select("SELECT COUNT(c.pk_id) as cant, CONCAT(n.nombre1,' ',n.apellido1) as nombre, n.pk_rif as rif
                            FROM cliente_natural n, cupon c
                            WHERE n.pk_rif = c.fk_cliente_natural
                            GROUP BY n.pk_rif
                            UNION
                            SELECT COUNT(c.pk_id), n.den_comercial as nombre, n.pk_rif as rif
                            FROM cliente_juridico n, cupon c
                            WHERE n.pk_rif = c.fk_cliente_juridico
                            GROUP BY n.pk_rif
                            ORDER BY cant DESC
                            LIMIT 10;");

        return view('reporte.puntos-clientes',['data'=>$data]);
    }
    
    public function ranking(){
        
        $data = DB::select("SELECT SUM(c.suma) as cant, c.rif as rif
            FROM (SELECT SUM(d.precio*d.cantidad) as suma , j.pk_rif as rif, v.pk_id as id
                    FROM factura v, detalle d, cliente_juridico j
                    WHERE d.pk_fk_factura =v.pk_id AND j.pk_rif=v.fk_cliente_juridico AND v.estado=false
                    GROUP BY j.pk_rif, v.pk_id) as c
            GROUP BY c.rif 
            UNION
            SELECT SUM(c.suma) as cant, c.rif
            FROM (SELECT SUM(d.precio*d.cantidad) as suma , j.pk_rif as rif, v.pk_id as id
                    FROM factura v, detalle d, cliente_natural j
                    WHERE d.pk_fk_factura =v.pk_id AND j.pk_rif=v.fk_cliente_natural AND v.estado=false
                    GROUP BY j.pk_rif, v.pk_id) as c
            GROUP BY c.rif 
            ORDER BY cant desc
            LIMIT 10;");

        return view('reporte.ranking-clientes',['data'=>$data]);
    }


    public function tiendasMasPunto(){
        $data = DB::select("SELECT COUNT(p.*) as cant, t.nombre as nombre 
                            FROM cupon p , factura f, tienda t
                            WHERE p.fk_factura_usa=f.pk_id AND t.pk_id=f.fk_tienda
                            GROUP BY t.nombre
                            ORDER BY cant DESC;");

        return view('reporte.tiendas-puntos',['data'=>$data]);
    }
}
