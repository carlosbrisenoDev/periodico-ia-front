<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/setContact", function (Request $r) {
    \App\contact_id::create([
        "phone" => $r->phone,
        "contact_id" => $r->cid,
        "queue" => $r->queue,
        "agent" => $r->agent
    ]);
    return json_encode(["ok" => "ok"]);
})->middleware("cors");