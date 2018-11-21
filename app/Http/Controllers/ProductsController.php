<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ProductsController extends Controller
{
    //
    public function create(){
        $categories = DB::select('select * from "Categoria"');
        
        //$categories = DB::select('select * from categoria where active = ?', [1]);

        return view('products.create', ['categories' => $categories]);
    }

    public function store(Request $request){
        $nombre = $request->input('nombre');
        $precio = $request->input('precio');
        $descripcion = $request->input('descripcion');
        $categoria = $request->input('categoria');

        // validar formulario
        $validateData = $this->validate($request, [
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'imagen' => 'required|image'
        ]);
        
        //subir la miniatura
        $imagen = $request->file('imagen');
        if($imagen){
            $imagepath = time().'-'.$imagen->getClientOriginalName();
            \Storage::disk('images')->put($imagepath, \File::get($imagen));
            $imageUrl = $imagepath;
        }

        DB::insert("insert into producto (pk_id, nombre, descripcion, precio, fk_categoria, imagen) values (nextval('producto_sequence'),?, ?,?,?,?)", [$nombre, $descripcion, $precio, $categoria, $imageUrl]);
        
        return redirect()->action('ProductsController@index');
    }

    public function index(){
        "";
        $products = DB::select('select p.pk_id as codigo, p.nombre as nombre, p.descripcion as descripcion, p.precio as precio, p.imagen as imagen,c.nombre as categoria from producto p, "Categoria" c where p.fk_categoria = c.pk_id');
        $ingredientes = DB::select('SELECT i.nombre as ingrediente, c.cantidad as cant, p.pk_id as prod FROM ingrediente i, composicion c, producto p  WHERE c.pk_fk_producto=p.pk_id AND c.pk_fk_ingrediente=i.pk_id ;');
        return view('products.index', ['products' => $products, 'ingredientes' => $ingredientes]);
    }

    public function edit(){
        echo '2';
        print ("el mio");
        return view('products.create');
    }

    public function update(Request $request){
        echo '1';
        
        print ("verdadero");
        return view('products.create');
    }


    public function getImage($fileName){
        $path =\Storage::disk('images')->getDriver()->getAdapter()->getPathPrefix().'/'.$fileName;
        return response()->file($path);
    }
}
