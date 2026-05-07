<?php

namespace App\Http\Middleware;

use Closure;

class authLevel
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
          if(\Auth::user() == null){
            return redirect("/")->with("status","Tu sesión ha expirado");
          }

          $blockedAliases = ['cliente', 'comunicacion', 'direccion', 'franquiciatario', 'marketing', 'observador', 'papeleria', 'relacionespublicas', 'rh'];
          $firstSegment = $request->segment(1);

          // Bloqueo global por módulo: si intentan entrar a rutas del alias bloqueado, se manda al home.
          if ($firstSegment && in_array($firstSegment, $blockedAliases, true)) {
            return redirect("/home")->with("status", "Este módulo está bloqueado en tu edición actual.");
          }

          // Usuarios pertenecientes a módulos bloqueados solo operan en su home/perfil.
          $userAlias = \Auth::user()->levels ? \Auth::user()->levels->alias : null;
          if ($userAlias && in_array($userAlias, $blockedAliases, true)) {
            $allowedPaths = ['home', 'edit', 'logout'];
            if (!in_array($firstSegment, $allowedPaths, true)) {
              return redirect("/home")->with("status", "Este módulo está bloqueado en tu edición actual.");
            }
          }

          return $next($request);

    }
}
