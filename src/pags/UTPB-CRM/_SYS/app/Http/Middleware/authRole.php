<?php

namespace App\Http\Middleware;

use Closure;

class authRole
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
          $roles = \Auth::user()->role;
          if(strstr($_SERVER["REQUEST_URI"],"finanzas")){
            foreach ($roles as $rl) {
              if($rl->role->role == "Finanzas"){
                return $next($request);
              }
            }
          }
          if(strstr($_SERVER["REQUEST_URI"],"estadisticas")){
            foreach ($roles as $rl) {
              if($rl->role->role == "Estadisticas"){
                return $next($request);
              }
            }
          }
          if(strstr($_SERVER["REQUEST_URI"],"leads")){
            foreach ($roles as $rl) {
              if($rl->role->role == "Leads"){
                return $next($request);
              }
            }
          }
          return redirect("/home?error")->with("status","No estas autorizado a acceder a este ROL");
    }
}
