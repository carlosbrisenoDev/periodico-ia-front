<?php
  $url = "http://".$_SERVER['SERVER_NAME']."/";
?>
<meta property="og:title" content="Unisant Orizaba - @yield('title','')" />
<meta property="og:type" content="website" />
<meta property="og:url" content="@yield('url',$url)" />
<meta property="fb:app_id" content="" />
<meta property="og:image" content="@yield('imagen',$url.'assets/images/logo.png')" />
<meta property="og:description" content="@yield('descripcion','')" />
<meta property="og:site_name" content="@yield('title')" />
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
