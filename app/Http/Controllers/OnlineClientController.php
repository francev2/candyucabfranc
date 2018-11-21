<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;

class OnlineClientController extends Controller
{
    //
    public function index(){
        $productos = DB::select('SELECT p.pk_id as codigo , p.nombre as nombre, p.descripcion as descripcion, p.precio as precio, p.imagen as imagen, c.nombre as categoria 
                                    FROM producto p, "Categoria" c WHERE c.pk_id = p.fk_categoria ;');
                                
        
        $user = Auth::user();
        $fac = DB::select("SELECT f.pk_id as codigo FROM factura f
                            WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                            ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id]);
        

        if (count($fac)==0){
            $totales = null;
        }else{
            $totales = DB::select("SELECT SUM(precio*cantidad) as total, COUNT(pk_fk_producto) as items
                                    FROM detalle d
                                    WHERE pk_fk_factura=?", [$fac[0]->codigo,]);
        }
        
        return view('online.index', ['productos'=>$productos, 'carrito'=>$totales[0]]);
    }

    public function addToCart(Request $request){
        $user = Auth::user();

        $fac = DB::select("SELECT f.pk_id as codigo FROM factura f
                            WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                            ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id]);

        if (is_null($user->fk_cliente_natural))
            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre, fk_tienda as tienda FROM cliente_juridico WHERE pk_rif = ?;", [$user->fk_cliente_juridico,])[0];
        else
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre, fk_tienda as tienda FROM cliente_natural WHERE pk_rif = ?;", [$user->fk_cliente_natural,])[0];
        
        if (count($fac)==0) {

            if(is_null($user->fk_cliente_natural)){
                DB::insert("INSERT INTO factura(fecha_creada, fecha_entrega,estado,tipo,fk_tienda,fk_usuario, fk_cliente_juridico) 
                            VALUES(?,?,true,'online',?,?,?)",[date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$cliente->tienda,$user->id,$user->fk_cliente_juridico]);
            }else{
                DB::insert("INSERT INTO factura(fecha_creada, fecha_entrega,estado,tipo,fk_tienda,fk_usuario, fk_cliente_natural) 
                            VALUES(?,?,true,'online',?,?,?)",[date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$cliente->tienda,$user->id,$user->fk_cliente_natural]);

            }
            $num_factura = DB::select("SELECT f.pk_id as codigo FROM factura f
                                        WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                                        ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id])[0]->codigo;
        }else{
            $num_factura = $fac[0]->codigo; 
        }

        $producto = $request->input('producto');
        $cantidad = $request->input('cantidad');
        $precio = DB::select("SELECT precio FROM producto where pk_id=?;", [$producto,])[0]->precio;
        DB::insert("INSERT INTO detalle(pk_fk_producto, pk_fk_factura, cantidad, precio) VALUES (?,?,?,?);",[$producto, $num_factura, $cantidad, $precio]);
        
        return redirect('store/index');
    }

    public function cart(){
        $user = Auth::user();

        $fac = DB::select("SELECT f.pk_id as codigo FROM factura f
                            WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                            ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id,]);

        
        if (count($fac)==0) {
            $detallados = null;
            $total = (object) ["precio"=>0, "cant"=>0];
            $detallados = array();
        }else{
            $num_factura = $fac[0]->codigo;
            $detallados = DB::select("SELECT d.pk_id as codigo, d.precio as precio, d.cantidad as cantidad, p.nombre as nombre, p.imagen as imagen, p.pk_id as id_prod FROM detalle d, producto p WHERE d.pk_fk_factura=? AND d.pk_fk_producto=p.pk_id;",[$num_factura]);
            
            $total = DB::select("SELECT SUM(precio*cantidad) as precio, COUNT(*)as cant FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,])[0];
        
            if (is_null($total)){
                $total->precio = 0;
                $total->cant = 0;
            }    
        }

        return view('online.cart', ['detallados'=>$detallados, 'total'=>$total->precio, 'cant'=>$total->cant]);
    }

    public function delete(Request $request)
    {
        $detalle = $request->input('codigo_detalle');

        DB::delete("DELETE FROM detalle WHERE pk_id=?;",[$detalle,]);

        return redirect('store/cart');
    }

    public function pago()
    {
        $user = Auth::user();

        $num_factura = DB::select("SELECT f.pk_id as codigo FROM factura f
                            WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                            ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id,])[0]->codigo;


        $f = DB::select("SELECT fk_cliente_natural as natural, fk_cliente_juridico as juridico FROM factura WHERE pk_id = ?;",[$num_factura,])[0];
        if($f->natural != null){
            $cliente = DB::select("SELECT pk_rif as rif, CONCAT(nombre1,' ',apellido1) as nombre FROM cliente_natural WHERE pk_rif = ?;", [$f->natural,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_natural = ? AND m.fk_debito_credito=dc.pk_id AND dc.visa_master<>'';",[$f->natural,]);
        
            $puntos = DB::select("SELECT c.pk_id as codigo, p.monto as monto
                                    FROM cupon c, punto p
                                    WHERE c.fk_cliente_natural=? AND c.pk_fk_punto=p.pk_id AND estado=false;",[$f->natural,]);
        }else{
            $cliente = DB::select("SELECT pk_rif as rif, den_comercial as nombre FROM cliente_juridico WHERE pk_rif = ?;", [$f->juridico,])[0];
            $metodos = DB::select("SELECT m.pk_id as codigo, m.banco as banco, dc.numero as numero, dc.visa_master as tipo
                                    FROM metodo m, debito_credito dc
                                    WHERE m.fk_cliente_juridico = ? AND m.fk_debito_credito=dc.pk_id AND dc.visa_master<>'';",[$f->juridico,]);
            $puntos = DB::select("SELECT c.pk_id as codigo, p.monto as monto
                                    FROM cupon c, punto p
                                    WHERE c.fk_cliente_juridico=? AND c.pk_fk_punto=p.pk_id AND estado=false;",[$f->juridico,]);
        }

        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle 
                            WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;

        
        
        $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
        if(!is_null($p[0]->monto)){
            $pagado = $p[0]->monto;
            $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
        }else{
            $pagado = null;
            $diferencia = null;
        }


        $puntos_usados = DB::select("SELECT c.pk_id as codigo, p.monto as monto
                                        FROM cupon c, punto p
                                        WHERE c.fk_factura_usa=? AND c.pk_fk_punto=p.pk_id AND estado=true;",[$num_factura,]);

        if(count($puntos_usados)>0){
            $t = 0;
            foreach($puntos_usados as $usado){
                $t += intval($usado->monto);  
            }
            
            if(is_null($p[0]->monto)){
                $pagado = $t;
                $diferencia = $total - $t;
            }else{
                $pagado = intval($pagado)+$t;
                $diferencia = intval($diferencia) - $t;
            }
        }

        return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos]);
    }


    public function postPago (Request $request){
        $user = Auth::user();

        $num_factura = DB::select("SELECT f.pk_id as codigo FROM factura f
                            WHERE f.tipo='online' AND f.estado=true AND tipo='online' AND fk_usuario=? 
                            ORDER BY f.fecha_creada DESC LIMIT 1;", [$user->id,])[0]->codigo;

        
        $metodo = $request->input('metodo');
        $todo = $request->input('all');
        $monto = $request->input('monto');
        $usa_puntos = $request->input('usa');

        $total = DB::select("SELECT SUM(precio*cantidad) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,])[0]->precio;
        
        $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
        if(!is_null($p[0]->monto)){
            $pagado = $p[0]->monto;
            $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
        }else{
            $pagado = null;
            $diferencia = null;
        }

        $puntos_usados = DB::select("SELECT c.pk_id as codigo, p.monto as monto
                                        FROM cupon c, punto p
                                        WHERE c.fk_factura_usa=? AND c.pk_fk_punto=p.pk_id AND estado=true;",[$num_factura,]);


        if(count($puntos_usados)>0){
            $t = 0;
            foreach($puntos_usados as $usado){
                $t += intval($usado->monto);  
            }
            
            if(!is_null($p[0]->monto)){
                $pagado = intval($pagado) + $t;
                $diferencia = intval($diferencia) - $t;
            }else{
                $pagado = $t;
                $diferencia = intval($total) - $t;
            }
        }




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




        $errors = new MessageBag();        

        
        $puntos = DB::select("SELECT c.pk_id as codigo, p.monto as monto
        FROM cupon c, punto p
        WHERE c.fk_cliente_juridico=? AND c.pk_fk_punto=p.pk_id AND estado=false;",[$cliente->rif,]);
        
        if($usa_puntos){

        $puntossel = $request->input('puntos');
        $total_punto = 0;
        foreach($puntossel as $punto){
            $total_punto += intval(DB::select("SELECT p.monto as monto
                                                FROM cupon c, punto p
                                                WHERE c.pk_fk_punto=p.pk_id AND c.pk_id=?;",[intval($punto),])[0]->monto);
        }




        if($diferencia == null){
            if($total_punto <= $total){
                foreach($puntossel as $punto){
                    DB::update("UPDATE cupon set estado = true, fk_factura_usa=? WHERE pk_id=?;",[$num_factura, $punto]);
                }

                if($total_punto == $total){
                    DB::update("UPDATE factura SET estado=false WHERE pk_id=?",[$num_factura]);        
                    return redirect("store/factura/".$num_factura);
                }
            }else{
                $errors->add('error', 'A seleccionado muchos puntos');
                return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos])->withErrors($errors);            
            }
        }else{
            if($total_punto <= $diferencia){
                foreach($puntossel as $punto){
                    DB::update("UPDATE cupon set estado = true, fk_factura_usa=? WHERE pk_id=?;",[$num_factura, $punto]);
                }

                if($total_punto == $diferencia){
                    DB::update("UPDATE factura SET estado=false WHERE pk_id=?",[$num_factura]);        
                    return redirect("store/factura/".$num_factura);
                }
            }else{
                $errors->add('error', 'A seleccionado muchos puntos');
                        return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos])->withErrors($errors);            
            }
        }



        
    }else{


        if (!is_null($metodo)){
            if(!$todo){
                if(is_null($monto)){
                    $errors->add('error', 'Ingresa el monto a pagar');
                    return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos,'puntos'=>$puntos])->withErrors($errors);
                }
            }
        }else{
            $errors->add('error', 'Ingresa el metodo a pagar');
            return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos])->withErrors($errors);
        }
        

        if($todo){
            if(is_null($pagado))
                DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$total, date('Y-m-d'), $metodo, $num_factura]);
            else
                DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$diferencia, date('Y-m-d'), $metodo, $num_factura]);
            
            DB::update("UPDATE factura SET estado=false WHERE pk_id=?",[$num_factura]);        
            return redirect("store/factura/".$num_factura);
        }else{
            if(is_null($pagado)){
                if($monto <= $total){
                        DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$monto, date('Y-m-d'), $metodo, $num_factura]);
                }else{    
                    $errors->add('error', 'El monto especificado es mayor que el total a pagar');
                    return view('venta.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos])->withErrors($errors);            
                }

                if($monto == $total){
                    DB::update("UPDATE factura SET estado=false WHERE pk_id=?",[$num_factura]);
                    return redirect("store/factura/".$num_factura);
                }else{
                    $p = DB::select("SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?;",[$num_factura,]);
                    if(count($p)>0){
                        $pagado = $p[0]->monto;
                        $diferencia = DB::select("SELECT (SUM(precio*cantidad)-(SELECT SUM(monto) as monto FROM pago WHERE fk_factura=?)) as precio FROM detalle WHERE pk_fk_factura = ?;",[$num_factura,$num_factura])[0]->precio;
                    }else{
                        $pagado = null;
                        $diferencia = null;
                    }
                    return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos]);    
                }
            }else{

                if($monto <= $diferencia)
                    DB::insert("INSERT INTO pago(monto, fecha, fk_metodo, fk_factura, efectivo) VALUES(?,?,?,?, false);",[$monto, date('Y-m-d'), $metodo, $num_factura]);
                else{    
                    $errors->add('error', 'El monto especificado es mayor el total a pagar');
                    return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos])->withErrors($errors);            
                }

                if($monto == $diferencia){
                    DB::update("UPDATE factura SET estado=false WHERE pk_id=?",[$num_factura]);
                    return redirect("store/factura/".$num_factura);
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
                    return view('online.pago', ['num_factura'=>$num_factura, 'cliente'=>$cliente, 'total' => $total, 'pagado' => $pagado, 'diferencia'=>$diferencia, 'metodos'=>$metodos, 'puntos'=>$puntos]);    
                    
                }
            }
        }
    }

    }

    
    public function factura($num_factura){
        $factura = DB::select("SELECT f.pk_id as codigo,CONCAT(t.nombre,'-',t.pk_id) as tienda, f.fk_cliente_natural as natural,
                                        f.fk_empleado as empleado, f.fk_cliente_juridico as juridico,
                                        CONCAT(l1.nombre,', Mun.',l2.nombre,', Edo.',l3.nombre) as dir
                                FROM factura f, tienda t, lugar l1, lugar l2, lugar l3, empleado e
                                WHERE f.pk_id=? AND f.fk_tienda=t.pk_id 
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
