<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    //
    public function create(){
        return view('venta.create');
    }

    public function store(Request $request){
        
        $tipo = $request->input('tipo');
        $rif = $request->input('rif');
        $empleado = Auth::user();

        $tienda = DB::select("SELECT fk_dep_tienda_tienda as tienda FROM empleado WHERE pk_cedula = ?",[$empleado->fk_empleado,])[0]->tienda;

        if ($tipo == 'N'){
            // validar formulario
            $validateData = $this->validate($request, [
                'tipo' => 'required',
                'rif' => 'required|exists:cliente_natural,pk_rif',
            ]);

            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$rif,])[0];
            DB::insert("INSERT INTO factura(fecha_creada,fecha_entrega,estado,tipo,fk_tienda,fk_empleado,fk_cliente_natural) VALUES(?,?,false,'fisica',?,?,?)",[date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$tienda,$empleado->fk_empleado,$cliente->rif]);
        }else{
            $validateData = $this->validate($request, [
                'tipo' => 'required',
                'rif' => 'required|exists:cliente_juridico,pk_rif',
            ]);

            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$rif,])[0];
            
            DB::insert("INSERT INTO factura(fecha_creada,fecha_entrega,estado,tipo,fk_tienda,fk_empleado,fk_cliente_juridico) VALUES(?,?,false,'fisica',?,?,?)",[date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$tienda,$empleado->fk_empleado,$cliente->rif]);
        }

        $productos = DB::select("SELECT pk_id as codigo, nombre, precio FROM producto ORDER BY nombre;");
        $num_factura = DB::select("SELECT currval('factura_sequence') as n;")[0]->n;

        return view('venta.add', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'productos'=>$productos, 'detallados'=>array()]);
    }

    public function add(Request $request, $num_factura){
        $validateData = $this->validate($request, [
            'producto' => 'required',
            'cantidad' => 'required|integer',
        ]);
        
        $producto = $request->input('producto');
        $cantidad = $request->input('cantidad');
        $precio = DB::select("SELECT precio FROM producto where pk_id=?;", [$producto,])[0]->precio;
        DB::insert("INSERT INTO detalle(pk_fk_producto, pk_fk_factura, cantidad, precio) VALUES (?,?,?,?);",[$producto, $num_factura, $cantidad, $precio]);
        $detallados = DB::select("SELECT d.pk_id as codigo, d.precio as precio, d.cantidad as cantidad, p.nombre as nombre, p.pk_id as id_prod FROM detalle d, producto p WHERE d.pk_fk_factura=? AND d.pk_fk_producto=p.pk_id;",[$num_factura]);
        $productos = DB::select("SELECT pk_id as codigo, nombre, precio FROM producto ORDER BY nombre;");
        
        $f = DB::select("SELECT fk_cliente_natural as natural, fk_cliente_juridico as juridico FROM factura WHERE pk_id = ?;",[$num_factura,])[0];
        if($f->natural != null){
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$f->natural,])[0];
        }else{
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(den_comercial) as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$f->juridico,])[0];
        }

        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;
        return view('venta.add', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'productos'=>$productos, 'detallados'=>$detallados, 'total' => $total]);
    }

    public function delete(Request $request, $num_factura){
        
        $detalle = $request->input('codigo_detalle');

        DB::delete("DELETE FROM detalle WHERE pk_id=?;",[$detalle,]);

        $detallados = DB::select("SELECT d.pk_id as codigo, d.precio as precio, d.cantidad as cantidad, p.nombre as nombre, p.pk_id as id_prod FROM detalle d, producto p WHERE d.pk_fk_factura=? AND d.pk_fk_producto=p.pk_id;",[$num_factura,]);
        $productos = DB::select("SELECT pk_id as codigo, nombre, precio FROM producto ORDER BY nombre;");
        
        $f = DB::select("SELECT fk_cliente_natural as natural, fk_cliente_juridico as juridico FROM factura WHERE pk_id = ?;",[$num_factura,])[0];
        if($f->natural != null){
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$f->natural,])[0];
        }else{
            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$f->juridico,])[0];
        }
        
        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;
        return view('venta.add', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'productos'=>$productos, 'detallados'=>$detallados, 'total' => $total]);
    }

    public function pago ($num_factura){

        $f = DB::select("SELECT fk_cliente_natural as natural, fk_cliente_juridico as juridico FROM factura WHERE pk_id = ?;",[$num_factura,])[0];
        if($f->natural != null){
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$f->natural,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_natural = ? AND m.fk_debito_credito=dc.pk_id;",[$f->natural,]);
        }else{
            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$f->juridico,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_juridico = ? AND m.fk_debito_credito=dc.pk_id;",[$f->juridico,]);
        }

        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle 
                            WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;
        
        $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
        if(count($p)>0){
            $pagado = $p[0]->monto;
            $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
        }else{
            $pagado = null;
            $diferencia = null;
        }

        return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos]);
    }

    public function postPago (Request $request, $num_factura){
        
        $metodo = $request->input('metodo');
        $todo = $request->input('all');
        $efectivo = $request->input('efectivo');
        $monto = $request->input('monto');

        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;
        
        $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
        if(count($p)>0){
            $pagado = $p[0]->monto;
            $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
        }else{
            $pagado = null;
            $diferencia = null;
        }

        $errors = new MessageBag();
        $f = DB::select("SELECT fk_cliente_natural as natural, fk_cliente_juridico as juridico FROM factura WHERE pk_id = ?;",[$num_factura,])[0];
        if($f->natural != null){
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$f->natural,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_natural = ? AND m.fk_debito_credito=dc.pk_id;",[$f->natural,]);
            $insert_cupon = "INSERT INTO cupon(pk_fk_punto,pk_fk_factura_creado, estado, fk_cliente_natural) VALUES(?,?,false,".$f->natural.");";
        }else{
            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$f->juridico,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_juridico = ? AND m.fk_debito_credito=dc.pk_id;",[$f->juridico,]);
            
            $insert_cupon = "INSERT INTO cupon(pk_fk_punto,pk_fk_factura_creado, estado, fk_cliente_juridico) VALUES(?,?,false,".$f->juridico.");";
        }

        if($efectivo){
            if(!$todo){
                if(is_null($monto)){
                    $errors->add('error', 'Ingresa el monto pagar');
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos])->withErrors($errors);
                }   
            }
        }else{
            if (!is_null($metodo)){
                if(!$todo){
                    if(is_null($monto)){
                        $errors->add('error', 'Ingresa el monto a pagar');
                        return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos])->withErrors($errors);
                    }
                }
            }else{
                $errors->add('error', 'Ingresa el metodo a pagar');
                return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos])->withErrors($errors);
            }
        }

        if($todo){
            if(is_null($pagado)){
                if(!$efectivo)
                    DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$total, date('Y-m-d'), $metodo, $num_factura]);
                else
                    DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,null,?, true);",[$total, date('Y-m-d'),$num_factura]);
            }else{
                if(!$efectivo)
                    DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$diferencia, date('Y-m-d'), $metodo, $num_factura]);
                else
                    DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,null,?, true);",[$diferencia, date('Y-m-d'),$num_factura]);
            }

            $cant_puntos = DB::select("SELECT COUNT(pk_fk_producto) as cant FROM detalle WHERE pk_fk_factura = ?;",[$num_factura])[0]->cant;
            $punto = DB::select("SELECT pk_id as codigo FROM punto WHERE fecha_fin is null ORDER BY fecha_inicio DESC LIMIT 1;")[0]->codigo;
            for ($i = 0; $i < intval($cant_puntos); $i++) {
                DB::insert($insert_cupon, [$punto, $num_factura]);
            }
            return redirect("admin/venta/factura/".$num_factura);
        }else{
            if(is_null($pagado)){
                if($monto <= $total){
                    if(!$efectivo)
                        DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$monto, date('Y-m-d'), $metodo, $num_factura]);
                    else
                        DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,null,?, true);",[$monto, date('Y-m-d'),$num_factura]);
                }else{    
                    $errors->add('error', 'El monto especificado es mayor que el total a pagar');
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos])->withErrors($errors);            
                }

                if($monto == $total){
                    $cant_puntos = DB::select("SELECT COUNT(pk_fk_producto) FROM detalle WHERE pk_fk_factura = ?;",[$num_factura]);
                    $punto = DB::select("SELECT TOP 1 pk_id as codigo FROM punto WHERE fecha_fin is null ORDER BY fecha_inicio DESC;")[0]->codigo;
                    for ($i = 0; $i < $cant_puntos; $i++) {
                        DB::insert($insert_cupon, [$punto, $num_factura]);
                    }
                    return redirect("admin/venta/factura/".$num_factura);
                }else{
                    $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
                    if(count($p)>0){
                        $pagado = $p[0]->monto;
                        $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
                    }else{
                        $pagado = null;
                        $diferencia = null;
                    }
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos]);    
                }
            }else{

                if($monto <= $diferencia){
                    if(!$efectivo)
                        DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$monto, date('Y-m-d'), $metodo, $num_factura]);
                    else
                        DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,null,?, true);",[$monto, date('Y-m-d'),$num_factura]);
                }else{    
                    $errors->add('error', 'El monto especificado es mayor el total a pagar');
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos])->withErrors($errors);            
                }

                if($monto == $diferencia){
                    $cant_puntos = DB::select("SELECT COUNT(pk_fk_producto) FROM detalle WHERE pk_fk_factura = ?;",[$num_factura]);
                    $punto = DB::select("SELECT TOP 1 pk_id as codigo FROM punto WHERE fecha_fin is null ORDER BY fecha_inicio DESC;")[0]->codigo;
                    for ($i = 0; $i < $cant_puntos; $i++) {
                        DB::insert($insert_cupon, [$punto, $num_factura]);
                    }
                    return redirect("admin/venta/factura/".$num_factura);
                }
                else{
                    $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
                    if(count($p)>0){
                        $pagado = $p[0]->monto;
                        $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
                    }else{
                        $pagado = null;
                        $diferencia = null;
                    }
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos]);    
                    
                }
            }
        }

    }


    public function factura($num_factura){
        $factura = DB::select("SELECT f.pk_id as codigo,CONCAT(t.nombre,'-',t.pk_id) as tienda, f.fk_cliente_natural as natural,
                                        CONCAT(e.nombres1,' ',e.apellido1) as empleado, f.fk_cliente_juridico as juridico,
                                        CONCAT(l1.nombre,', Mun.',l2.nombre,', Edo.',l3.nombre) as dir
                                FROM factura f, tienda t, lugar l1, lugar l2, lugar l3, empleado e
                                WHERE f.pk_id=? AND f.fk_tienda=t.pk_id AND f.fk_empleado=e.pk_cedula
                                        AND t.fk_lugar=l1.pk_id AND l1.fk_lugar=l2.pk_id AND l2.fk_lugar=l3.pk_id;",[$num_factura,])[0];
        
        
        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura])[0]->precio;
        
        $detallados = DB::select("SELECT d.pk_id as codigo, d.precio as precio, d.cantidad as cantidad, p.nombre as nombre, p.pk_id as id_prod FROM detalle d, producto p WHERE d.pk_fk_factura=? AND d.pk_fk_producto=p.pk_id;",[$num_factura]);
        
        if($factura->natural != null){
            $cliente = DB::select("SELECT n.pk_rif as rif, 
                                            CONCAT(n.nombre1,' ',n.apellido1) as nombre,
                                            CONCAT(l1.nombre,', Mun.',l2.nombre,', Edo.',l3.nombre) as dir
                                    FROM cliente_natural n, lugar l1, lugar l2, lugar l3, empleado e
                                    WHERE n.pk_rif=? AND n.fk_lugar=l1.pk_id AND l1.fk_lugar=l2.pk_id AND l2.fk_lugar=l3.pk_id;", [$factura->natural,])[0];
        }else{
                $cliente = DB::select("SELECT n.pk_rif as rif, 
                                                n.razon_social as nombre,
                                                CONCAT(l1.nombre,', Mun.',l2.nombre,', Edo.',l3.nombre) as dir
                                        FROM cliente_juridico n, lugar l1, lugar l2, lugar l3, empleado e
                                        WHERE n.pk_rif=? AND n.fk_lugar=l1.pk_id AND l1.fk_lugar=l2.pk_id AND l2.fk_lugar=l3.pk_id;", [$factura->juridico,])[0];
        }

        return view("venta.factura",['num_factura'=>$num_factura,'detallados'=>$detallados, 'cliente'=>$cliente, 'total' => $total, 'factura'=>$factura]);
    }

}
