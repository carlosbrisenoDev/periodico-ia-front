@php
  if(Auth::user()->level_id == NULL){
    $route = "users.cliente.home";
  } else {
    $route = "users.".Auth::user()->level->alias.".home";
  }
@endphp
@extends($route)
