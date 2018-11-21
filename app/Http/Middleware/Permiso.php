<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Permiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        //verificar si el usuario esta loggueado
        if (Auth::check()){
            $user = Auth::user();
            $permisos = DB::select("SELECT p.nombre as nombre FROM privilegio p, rol_privilegio rp WHERE rp.pk_fk_rol=? AND rp.pk_fk_privilegio=p.pk_id;",[$user->fk_rol]);
            $privilegios = array();
            foreach($permisos as $p){
                array_push($privilegios, $p->nombre);
            }
            //Verificar si es una pagina administracion
            if ($request->is('admin/*')) {
                //verificar si el usuario es de empleado
                if ($user->fk_empleado == null){
                    return redirect('registro-cliente');
                }else{
                    // ver que pagina accede
                    if ($request->is('*/diariocandy*') && !in_array("admin", $privilegios)){
                        if($request->isMethod('post') || $request->is('*/diariocandy/create')){
                            if(!in_array("crear diariocandy", $privilegios))
                                return redirect('registro-cliente');                    
                        }else if ($request->isMethod('put') || $request->is('*/diariocandy/*/edit')){
                            if(!in_array("actualizar diariocandy", $privilegios))
                                return redirect('registro-cliente');
                        }else if ($request->isMethod('delete')){
                            if(!in_array("borrar diariocandy", $privilegios))
                                return redirect('registro-cliente');
                        }else if ($request->isMethod('get') && $request->is('*/diariocandy')){
                            if(!in_array("crear diariocandy", $privilegios) && !in_array("actualizar diariocandy", $privilegios) && !in_array("borrar diariocandy", $privilegios) )
                                return redirect('registro-cliente');
                             
                        }
                            
                    }else if ($request->is('*/usuarios*') && !in_array("admin", $privilegios)){
                        if($request->isMethod('post') || $request->is('*/usuarios/create') || $request->is('*/usuarios/delete')){
                            if(!in_array("crear usuario", $privilegios))
                                return redirect('registro-cliente');                    
                        }else if ($request->isMethod('put') || $request->is('*/usuarios/*/edit')){
                            if(!in_array("actualizar usuario", $privilegios))
                                return redirect('registro-cliente');
                        }else if ($request->isMethod('post')){
                            if(!in_array("borrar usuario", $privilegios))
                                return redirect('registro-cliente');
                        }else if ($request->isMethod('get') && $request->is('*/usuarios')){
                            if(!in_array("crear usuario", $privilegios) && !in_array("actualizar usuario", $privilegios) && !in_array("borrar usuario", $privilegios) )
                                return redirect('registro-cliente');
                        }
                    }else if ($request->is('*/venta*') && !in_array("admin", $privilegios)){                    
                        if ($request->isMethod('get') || $request->isMethod('post')){
                            if(!in_array("venta en tienda", $privilegios))
                                return redirect('registro-cliente');
                        }
                    }else if ($request->is('*/venta*') && !in_array("admin", $privilegios)){                    
                        if ($request->isMethod('get') || $request->isMethod('post')){
                            if(!in_array("ver reportes", $privilegios))
                                return redirect('registro-cliente');
                        }
                    }else if (!in_array("admin", $privilegios)){
                        return redirect('registro-cliente');
                    }
                }
                
        
            }else if($request->is('store/*')){
                if (is_null($user->fk_cliente_natural) && is_null($user->fk_cliente_juridico) ){
                    return redirect('registro-cliente');
                }else{
                }
            }
        }else{
            return redirect('registro-cliente');
        }

        return $next($request);
    }
}
