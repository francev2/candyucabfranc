<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //registro de cliente
    public function register(Request $request){
        $email = $request->input('emailRegister');
        $p = $request->input('passwordRegister');
        $password = bcrypt($p);
        
        $tienda = $request->input('tienda');
        $consecutivo = $request->input('consecutivo');
        $rif = $request->input('rif');

        // validar formulario
        $validateData = $this->validate($request, [
            'emailRegister' => 'required|email|unique:users,email',
            'passwordRegister' => 'required',
            'tienda' => 'required|integer',
            'consecutivo' => 'required|integer',
            'rif' => 'required|integer',
        ]);

        $juridico = DB::select("SELECT pk_rif, den_comercial as nombre FROM cliente_juridico WHERE fk_tienda = ? AND consecutivo = ?;",[$tienda, $consecutivo]);
        $natural = DB::select("SELECT pk_rif, CONCAT(nombre1,'  ', apellido1) as nombre FROM cliente_natural WHERE fk_tienda = ? AND consecutivo_carnet = ?;",[$tienda, $consecutivo]);
        
        $errors = new MessageBag();
        
        if (count($juridico) == 0 && count($natural)==0  ){
            $errors->add('error', 'El codigo de carnet no existe');
            return view('register')->withErrors($errors);
        }else{
            if(count($juridico) != 0){
                $rifRe = $juridico[0]->pk_rif;
                $nombre = $juridico[0]->nombre;
                if ($rifRe != $rif){
                    $errors->add('error', 'El codigo del carnet no coincide con el rif proporcionado');
                    return view('register')->withErrors($errors);
                }else{
                    DB::insert("insert into users (email, password, fk_cliente_natural, fk_cliente_juridico, fk_rol, name, created_at, updated_at) values (?,?, null,?,1,?,?,?)", [$email, $password, $rifRe, $nombre, date('Y-m-d H:i:s'),date('Y-m-d H:i:s')]);
                    return redirect('');
                }
            }else if (count($natural) != 0){
                $rifRe = $natural[0]->pk_rif;
                $nombre = $natural[0]->nombre;
                if ($rifRe != $rif){
                    $errors->add('error', 'El codigo del carnet no coincide con el rif proporcionado');
                    return view('register')->withErrors($errors);
                }
                else{
                    DB::insert("insert into users (email, password, fk_cliente_juridico, fk_cliente_natural, fk_rol, name, created_at, updated_at ) values (?,?, null,?,1,?,?,?)", [$email, $password, $rifRe, $nombre, date('Y-m-d H:i:s'),date('Y-m-d H:i:s')]);
                    return redirect('');
                }
            }
        }
        
        
    }   
    
    public function getRegister(Request $request){
        return view('register');
    }   
    
    public function getCreateUser(){
        $user = Auth::user();
        $empleados = DB::select("SELECT e.pk_cedula as cedula, CONCAT(e.nombres1,' ',e.apellido1) as nombre, d.nombre as departamento FROM empleado e, departamento d WHERE e.pk_cedula <> ? AND d.pk_id=e.fk_dep_tienda_departamento ORDER BY pk_cedula",[$user->fk_empleado,]);
        $roles = DB::select("SELECT pk_id as codigo, nombre FROM rol ORDER BY nombre;");
        return view('usuario.create', ['empleados'=>$empleados, 'roles'=>$roles]);
    }   

    public function postCreateUser(Request $request){
        $email = $request->input('email');
        $p = $request->input('password');
        $password = bcrypt($p);
        $empleado = $request->input('empleado');
        $rol = $request->input('rol');

        // validar formulario
        $validateData = $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'empleado' => 'required|integer',
            'rol' => 'required|integer',
        ]);

        $nombre = DB::select("SELECT CONCAT(nombres1,' ',apellido1) as nombre FROM empleado WHERE pk_cedula = ?", [$empleado,])[0]->nombre;
        DB::insert("INSERT INTO users (email, password,  fk_empleado, fk_rol, name, created_at, updated_at ) values (?,?,?,?,?,?,?)", [$email, $password, $empleado, $rol, $nombre,date('Y-m-d H:i:s'),date('Y-m-d H:i:s')]);
        return redirect()->action('UserController@getIndexUser');
    }

    public function getIndexUser(){
        $usuarios = DB::select("SELECT id, email, name, nombre as rol FROM users, rol WHERE fk_rol=pk_id;");
        return view('usuario.index', ['usuarios'=>$usuarios,]);
    }

    public function deleteUser(Request $request){

        try {
            $user = $request->input('codigo');        
            DB::delete("DELETE FROM users WHERE id=?;", [$user,]);
            return redirect('admin/usuarios');
        }catch (QueryException $e) {
            return redirect('admin/usuarios')->with('status', 'No se puede borrar, tiene asignada claves foraneas en carrito o facturas');

        }    

    } 

    public function edit($id){
        $usuario =  DB::select('SELECT id, email, name, fk_rol as rol from users WHERE id = ?;',[$id,]);
        $roles = DB::select("SELECT pk_id as codigo, nombre FROM rol ORDER BY nombre;");
        
        return view('usuario.update', ["usuario"=>$usuario[0], 'roles'=>$roles]);
    }

    public function update(Request $request, $id){

        $email = $request->input('email');
        $name = $request->input('name');
        $rol = $request->input('rol');

        // validar formulario
        $validateData = $this->validate($request, [
            'email' => ['required','email',Rule::unique('users')->ignore($id)],
            'name' => 'required',
            'rol' => 'required|integer',
        ]);

        $p = $request->input('password');
        
        if ($p != null){
            $password = bcrypt($p);
            DB::update("UPDATE users SET email = ?, name =  ?, fk_rol = ?, password = ? WHERE id=?", [$email, $name,$rol, $password, $id]);
        }else{
            DB::update("UPDATE users SET email = ?, name =  ?, fk_rol = ? WHERE id=?", [$email, $name,$rol, $id]);
        }
        
        return redirect()->action('UserController@getIndexUser');
    }

}
