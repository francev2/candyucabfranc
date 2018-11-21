<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'store', 'middleware' => ['permiso']], function(){
    Route::get('index','OnlineClientController@index');
    Route::get('cart','OnlineClientController@cart');
    Route::post('add','OnlineClientController@addToCart');
    Route::post('delete','OnlineClientController@delete');
    Route::get('pago','OnlineClientController@pago');
    Route::post('pago','OnlineClientController@postPago');
    Route::get('factura/{num_factura}','OnlineClientController@factura');
});


//Route::get('/productos/create', 'ProductsController@create');

Route::group(['prefix' => 'admin', 'middleware' => ['permiso']], function(){

    Route::resource('diariocandy','DiarioController');
    Route::get('diariocandy/diario/{id}/add','DiarioController@getAddProduct');
    Route::post('diariocandy/diario/add','DiarioController@postAddProduct');
    Route::get('diariocandy/diario/{id}/delete','DiarioController@getDeleteProduct');
    Route::post('diariocandy/diario/delete','DiarioController@postDeleteProduct');
    Route::post('diariocandy/diario/delete','DiarioController@deleteDiario');

    
    Route::get('usuarios/create','UserController@getCreateUser');
    Route::post('usuarios','UserController@postCreateUser');
    Route::get('usuarios','UserController@getIndexUser');
    Route::post('usuarios/delete','UserController@deleteUser');
    Route::get('usuarios/{id}/edit','UserController@edit');
    Route::post('usuarios/{id}/edit','UserController@update');

    Route::resource('products','ProductsController');
    Route::resource('rol','RolController');
    Route::resource('privilegio','PrivilegioController');

    Route::get('venta','VentaController@create');
    Route::post('venta','VentaController@store');
    Route::post('venta/{num_factura}/add','VentaController@add');
    Route::post('venta/{num_factura}/delete','VentaController@delete');
    Route::get('venta/{num_factura}/pago','VentaController@pago');
    Route::post('venta/{num_factura}/pago','VentaController@postPago');
    Route::get('venta/factura/{num_factura}','VentaController@factura');

    
    Route::get('reporte/cliente-frecuente','ReporteController@clienteFrecuente');
    Route::post('reporte/cliente-frecuente','ReporteController@postClienteFrecuente');
    Route::get('reporte/mejor-cliente','ReporteController@mejorCliente');
    Route::post('reporte/mejor-cliente','ReporteController@postMejorCliente');
    Route::get('reporte/presupuesto-efectivo','ReporteController@presupuesto');
    Route::post('reporte/presupuesto-efectivo','ReporteController@postPresupuesto');
    Route::get('reporte/mas-vendido','ReporteController@masVendido');
    Route::post('reporte/mas-vendido','ReporteController@postMasVendido');
    Route::get('reporte/puntos-clientes','ReporteController@puntosClientes');
    Route::get('reporte/ranking-clientes','ReporteController@ranking');
    Route::get('reporte/tiendas-puntos','ReporteController@tiendasMasPunto');

    //Route::resource('categories','CategoryController')->except([
    //    'show', 'edit', 'update'
    //]);
});


Route::get('/images/{fileName}', array (
    'as' => 'imagenProducto',
    'uses' => 'ProductsController@getImage'
));

Route::post('/registro-cliente', 'UserController@register');
Route::get('/registro-cliente', 'UserController@getRegister');
Route::post('/the-login', 'UserController@login');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
