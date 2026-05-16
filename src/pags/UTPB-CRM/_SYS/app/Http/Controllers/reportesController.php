<?php

namespace App\Http\Controllers;

use App\Mail\NotificacionReporte;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reportesController extends Controller
{
    public function index()
    {
        return view('reportes.list');
    }

    public function mylist()
    {
        return view('reportes.mylist');
    }

    public function reporte($id)
    {
        return view('reportes.respuestas', compact('id'));
    }

    public function mireporte($id)
    {
        return view('reportes.mireporte', compact('id'));
    }

    public function respuestas($id)
    {
        return view('reportes.todaslasrespuestas', compact('id'));
    }

    public function test()
    {
        return view('reportes.test');
    }

    public function make(Request $request)
    {
        $source_file = null;
        // if($request->hasFile("file")){
        //     $file = $request->file("file");
        //     $filedata = file_get_contents($file->getRealPath());
        //     $data = explode('.',$file->getClientOriginalName());
        //     $name = "";
        //     for($k = 0; $k < count($data)-1;$k++)
        //     {
        //      $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        //     }
        //     $ext = $data[count($data)-1];
        //     $document = \App\documento::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>auth()->user()->id]);
        //     \Storage::cloud('s3')->put(md5($document->id).".file",$filedata);
        //     $datos["location"] = \Cloud::url($document->id);
        //     $source_file = $datos["location"];
        // }

        $reporte_c = \App\reportes_tickets::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'areas' => json_encode($request->area),
            'usuarios' => json_encode($request->users),
            // 'file_upload' => $source_file,
            'user_id' => auth()->user()->id,
            'prioridad' => intval($request->prioridad),
            'file_name' => $request->fileTitle ?? null,
            'estado' => 0,
        ]);
        foreach ($request->users as $uid) {
            try {
                $uas = \App\user::find($uid);
                echo $uas;
                if ($uas->email) {
                    \Mail::to([$uas->email])->send(new NotificacionReporte('Notificación de Nuevo Reporte', auth()->user()->name . ' Creo un nuevo reporte - ' . $request->titulo, auth()->user(), $uas, $reporte_c));
                }
            } catch (\Exception $e) {
//                 \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new FirmarCliente($c,$r->razon));
            }
        }

        // return view('reportes.create')->with("status","Reporte Creado");
        return redirect(url('/mireporte/' . md5($reporte_c->id)));
    }

    public function create()
    {
        return view('reportes.create');
    }

    public function response(Request $request)
    {
        // dd($request);
        $reporte = \App\reportes_tickets::where(DB::raw('md5(id)'), $request->reporte)->first();
        $redirect_a = null;
        $redirect_u = null;
        if ($request->users) {
            $redirect_u = json_encode($request->users);
        }

        if ($request->area) {
            $redirect_a = json_encode($request->area);
        }

        \App\respuestas_reportes::create([
            'id_reporte_ticket' => $reporte->id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'usuario' => auth()->user()->id,
            'estado' => $request->estado ?? 1,
            'prioridad' => $request->prioridad ?? 1,
            'redireccion_areas' => $redirect_a,
            'redireccion_usuarios' => $redirect_u,
            'desc_fallo' => $request->fallo,
        ]);

        if ($request->prioridad != $reporte->prioridad) {
            $reporte->update(['prioridad' => $request->prioridad]);
        }
        if ($redirect_a) {
            $reporte->update(['areas' => $redirect_a]);
        }
        if ($redirect_u) {
            $reporte->update(['usuarios' => $redirect_u]);
        }
        $reporte->update(['estado' => $request->estado]);

        return redirect()->back()->with("status", "Respuesta Enviada");

    }

    public function getUserPerArea(Request $request)
    {
        if ($request->id[0] != 'all') {
            $users = \App\User::whereIn(DB::raw('md5(level_id)'), $request->id)->get();
        } elseif ($request->id[0] === 'all') {
            $users = \App\User::get();
        }

        return $users;
    }

    public function refresh(Request $request)
    {

        $reporte = \App\reportes_tickets::where('id', $request->id)->first();
        if (!$reporte) {
            return redirect()->back()->with("status", "Algo Falló");
            // return redirect(url('/mireporte/'.md5($reporte_c->id)));
        }
        $reporte->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad_id,
        ]);
        // return view('reportes.create')->with("status","Reporte Creado");
        return redirect(url('/mireporte/' . md5($reporte->id)))->with("status", "Reporte actualizado");
    }

    public function fileupload(Request $request)
    {
        $reporte = \App\reportes_tickets::where(DB::raw('md5(id)'), $request->rep_id)->first();
        if ($reporte) {
            $source_file = null;
            $name_file = null;
            $ext_file = null;
            if ($request->hasFile("file")) {
                $file = $request->file("file");
                $filedata = file_get_contents($file->getRealPath());
                $data = explode('.', $file->getClientOriginalName());
                $name = "";
                for ($k = 0; $k < count($data) - 1; $k++) {
                    $name .= $data[$k] . (($k == (count($data) - 2)) ? "" : ".");
                }
                $ext = $data[count($data) - 1];
                $document = \App\documento::create(['size' => $file->getSize(), 'ext' => $ext, 'titulo' => $name, 'empleado_id' => auth()->user()->id, 'reporte_id' => $reporte->id]);
                \Storage::cloud('s3')->put(md5($document->id) . ".file", $filedata);
                $datos["location"] = \Cloud::url($document->id);
                $document->update(['url' => $datos["location"]]);
            }
        } else {
            return 0;
        }
        // return view('reportes.create')->with("status","Reporte Creado");
        return $datos["location"];
    }

}
