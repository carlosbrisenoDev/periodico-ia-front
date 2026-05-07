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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

Route::get('/flare', function(){
     return Artisan::call('config:clear');
});

Route::get("/documentos/watchar/{code}",function($code, Request $r){
  return App::make('App\Http\Controllers\\documentos')->watchar($r,$code);
})->where(["code"=>"[A-z0-9]+"]);

Route::get("/file/{code}", function ($code, Request $r) {
  return \Cloud::get($code);
});

Route::post("/api/clientes", function (Request $r) {
  return Response::json(\App\cliente::create($r->all()));
})->middleware("cors");

Route::post("/api/setcliente", function (Request $r) {
  $cliente = \App\cliente::find($r->cliente_id);
  if($cliente->agente_id != NULL){
    return json_encode(["error"=>"El cliente ya ha sido asignado a otro usuario"]);
  }
  $cliente->update([
    "agente_id" => $r->usuario_id
  ]);
  return Response::json($cliente);
})->middleware("cors");

Route::get("/softphone", function (Request $r) {
  return view("layouts.aws.softphone");
});
Route::get("/omnichannel", function (Request $r) {
  return view("layouts.omnichannel");
});
Route::get("/softphone-i", function (Request $r) {
  return view("layouts.aws.index-invisible");
});
Route::get("/c/mail", function (Request $r) {
  return view("components.mail");
});

Route::any('/cartera/api', 'cartera@api_pagos');
Route::any("/webhook", "webhook@facebook")->name('webhook');
Route::any("/t","webhook@test");
Route::get("/payment", "PayPalController@payment")->name('payment');
Route::get('/cancel', 'PayPalController@cancel')->name('payment.cancel');
Route::get('/payment/success', 'PayPalController@success')->name('payment.success');
Route::get('/calculadora', 'calculadoraController@index')->name('calculadora.index');
Route::get('/calculadora/misDatos', 'calculadoraController@misDatos')->name('calculadora.misDatos');
Route::get('/calculadora/datosGenerales', 'calculadoraController@datosGenerales')->name('calculadora.datosGenerales');

Route::get('/empresas/list', 'empresasController@index')->name('empresas.list');
Route::get('/empresas/crear', 'empresasController@create')->name('empresas.create');
Route::get('/empresas/edit/{id}', 'empresasController@edit')->name('empresas.edit');

Route::post('/empresas/make', 'empresasController@make')->name('empresas.make');
Route::post('/empresas/update', 'empresasController@update')->name('empresas.update');
Route::post('/empresas/destroy', 'empresasController@destroy')->name('empresas.destroy');
Route::post('/empresas/empresas_productos', 'empresasController@empresas_productos')->name('empresas.empresas_productos');


Route::get('/productos/list', 'productosController@index')->name('prodcuto.list');
Route::get('/productos/crear', 'productosController@create')->name('prodcuto.create');
Route::get('/productos/edit/{id}', 'productosController@edit')->name('prodcuto.edit');
Route::post('/productos/make', 'productosController@make')->name('productos.make');
Route::post('/productos/update', 'productosController@update')->name('productos.update');
Route::post('/productos/destroy', 'productosController@destroy')->name('productos.destroy');

Route::get('/calculadora/registros/registro/{id}', 'calculadoraController@registro')->name('calculadora.registro');
Route::post('/api/empresas/getAll', 'calculadoraController@empresasGetAll')->name('calculadora.empresasGetAll');
Route::post('/api/pasarelas/getAll', 'calculadoraController@pasarelasGetAll')->name('calculadora.pasarelasGetAll');
Route::post('/api/productos/getAll', 'productosController@productosGetAll')->name('productos.productosGetAll');
Route::post('/api/materias/getAll', 'materiasController@materiasGetAll')->name('materias.materiasGetAll');
Route::post('/api/actividades/getByDate', 'actividadesController@getByDate')->name('actividades.getByDate');
Route::post('/api/actividades/getByDate/general', 'actividadesController@getByDateGeneral')->name('actividades.getByDateGeneral');
Route::post('/calculadora/data/save', 'calculadoraController@saveData')->name('calculadora.saveData');
Route::post('/login/fastLogin', 'fastLogin@fastLogin')->name('fastLogin');

// ACTIVIDADES
Route::get('/actividades/create/', 'actividadesController@create')->name('actividades.create');
Route::post('/actividades/register/', 'actividadesController@register')->name('acividades.register');
Route::get('/actividad/info/{act}', 'actividadesController@view')->name('actividades.view');
Route::get('/actividad/user/info/{userid}', 'actividadesController@viewperuser')->name('actividades.viewperuser');
Route::get('/actividades/list', 'actividadesController@list')->name('actividades.list');
Route::get('/actividades/global/list', 'actividadesController@glist')->name('actividades.glist');
Route::get('/actividadesCatalogo/create', 'actividadesController@createCat')->name('actividades.createCat');
Route::post('/actividadesCatalogo/register', 'actividadesController@registerCat')->name('actividades.registerCat');
Route::get('/actividadesCatalogo/list', 'actividadesController@listCat')->name('actividades.listCat');
Route::post('/api/actividades/getByDate/byuser', 'actividadesController@getByDatebyuser')->name('actividades.getByDatebyuser');
Route::post('/api/actividades/getByDate/general/byuser', 'actividadesController@getByDateGeneralbyuser')->name('actividades.getByDateGeneralbyuser');

Route::get('/api/actividades/getAll/general/{token}', 'actividadesController@getAllDataInFile')->name('actividades.getAllDataInFile');
Route::get('/api/clientes/getAll/inscritos/{token}', 'clientes@getAllDataInFile')->name('clientesInscritos.getAllDataInFile');
Route::get('/test', 'HomeController@test')->name('home.test');
Route::any('/api/client/sinapissste/{token}', 'clientes@crmSinapisssteClient')->name('client.crmSinapisssteClient');
Route::any('/api/client/web-form/{token}', 'clientes@crmClient')->name('client.crmClient');
Route::any('/api/client/web-register',['middleware' => 'cors', 'uses' => 'clientes@registerClient'])
->name('client.registerClient');
Route::any('/api/client/emg/web-register/{token}',['middleware' => 'cors', 'uses' => 'clientes@crmClientEmg'])
->name('client.crmClientEmg');


Route::get('/sms/file/', 'altaria@index')->name('altaria.index');
Route::post('/sms/file/altaria/', 'altaria@filepost')->name('altaria.filepost');




//-------------- VENTAS ----------------------//
Route::get('/ventas/cliente/removeClient/{id}', 'ventas@removeClient')->name('ventas.removeClient');

//-------------- METAS ----------------------//
Route::get('/metas', 'metasController@metas')->name('metas.index');
Route::post('/metas/setmeta', 'metasController@setmeta')->name('metas.set');

// ---------------tickets -----------------//
Route::get('/reporte/mylist', 'reportesController@mylist')->name('reporte.mylist');
Route::get('/reporte/list', 'reportesController@index')->name('reporte.list');
Route::get('/reporte/crear', 'reportesController@create')->name('reporte.crear');
Route::get('/reporte/test', 'reportesController@test')->name('reporte.test');
Route::get('/reporte/{id}', 'reportesController@reporte')->name('reporte.reporte');
Route::get('/mireporte/{id}', 'reportesController@mireporte')->name('reporte.mireporte');
Route::get('/reporte/todaslasrespuestas/{id}', 'reportesController@respuestas')->name('reporte.respuestas');
Route::post('/reporte/response', 'reportesController@response')->name('reporte.response');
Route::post('/reporte/make', 'reportesController@make')->name('reporte.make');
Route::post('/reporte/getUserPerArea', 'reportesController@getUserPerArea')->name('reporte.getUserPerArea');
Route::post('/reporte/fileupload', 'reportesController@fileupload')->name('reporte.fileupload');
Route::post('/reportes/refresh', 'reportesController@refresh')->name('reporte.refresh');




//-------------- VENTAS ----------------------//
Route::get('/edit/info/{id}', 'HomeController@editmyinfo')->name('home.edit');
Route::post('/home/updateinfo', 'HomeController@updateinfo')->name('home.updateinfo');

Route::get('/', function () {
  if(Auth::user() == null){
    return view("ws.default");
  }else{
    return redirect("/home");
  }
});
Route::get('/signature', function () {
    return view("signature");
});
Route::get('/process', function () {
    return view("process");
});
Route::post('/alumnos/signvideo', function(Request $r) {
  return App::make('App\Http\Controllers\\alumnos')->signvideo($r);
});
Route::get('watchar/{someSubCommand}',function($someSubCommand,Request $r){
  return App::make('App\Http\Controllers\\gaceta')->watchar($r,$someSubCommand);
});
Route::get('video/{someSubCommand}',function($someSubCommand,Request $r){
  return App::make('App\Http\Controllers\\alumnos')->video($r,$someSubCommand);
});

Route::group(["prefix"=>"g"],function(){
  Route::get('/',function(){return view("g.default");});

  Route::get('{s}',function($s="default"){
    return view("website.$s");
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);

  Route::post('{someCommand}/{someSubCommand}',function($someCommand,$someSubCommand,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

  Route::get('{someCommand}/{someSubCommand}/{someSubCommand2}',function($someCommand,$someSubCommand,$someSubCommand2,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r,$someSubCommand2);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

});

Route::group(["prefix"=>"ws"],function(){
  Route::get('/',function(){return view("ws.default");});

  Route::get('{s}',function($s="default"){
    return view("ws.$s");
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);

  Route::get('{someCommand}/{someSubCommand}/{someSubCommand2}',function($someCommand,$someSubCommand,$someSubCommand2,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r,$someSubCommand2);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

});

Route::group(["middleware"=>"auth.role"],function(){
  Route::get('r/{s}',function($f,$s="default"){
    return view("roles.$f.$s");
  });
});

Route::group(["middleware"=>"auth.level"],function(){
  Route::get('otherwise/{s}',function($s){
    return view("users.".\App\Auth::user()->level->alias.".$s");
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);



  Route::post('{someCommand}/{someSubCommand}',function($someCommand,$someSubCommand,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

  Route::get('{someCommand}/{someSubCommand}/{someSubCommand2}',function($someCommand,$someSubCommand,$someSubCommand2,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r,$someSubCommand2);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);


  Route::get('{n}/{s}',function($n,$s){
    return view("users.$n.$s");
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);
  Route::get('/home', 'HomeController@index')->name('home');
});


Auth::routes();

Route::get("/login",function(){
  return redirect("/");
})->name("login");
