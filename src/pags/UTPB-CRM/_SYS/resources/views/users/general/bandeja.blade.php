@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                  <?php
                    try {
                      $box1 = "";
                      $rebox = "";
                      $con->getMailboxes();
                  ?>
                  <div class="row">
                    <div class="col-md-2 col-sm-12 text-center">
                      <a href="/bandeja/nuevo/correo" class="btn btn-link">
                        <i class="fa fa-plus"></i> Redactar nuevo correo
                      </a>
                    </div>
                    @php
                    $names = ["Notes"=>"Notas","Archive"=>"Archivo","spam"=>"No deseados","Sent"=>"Enviados","INBOX"=>"Recibidos","Drafts"=>"Borrador","Trash"=>"Eliminados","Junk"=>"Basura"];
                    @endphp
                    @if (!isset($_REQUEST["mail"]))
                    <div class="col-md-10 col-sm-12">
                      <div class="row">
                        <div class="col-1">
                        </div>
                        <div class="col">

                        </div>
                        <div class="col-sm-12 col-md">
                          <form action="/bandeja/correo/listar" method="get">
                            <input type="text" style="width:100%;" class="form-control" id="buscar" value="{{isset($_REQUEST["buscar"]) ? $_REQUEST["buscar"]:"" }}" name="buscar" placeholder="Buscar ...">
                          </form>
                        </div>
                        <div class="col-md-2 col-sm-12">
                          <select class="form-control mover">
                            <option value="0">Mover a</option>
                            @foreach ($con->getMailboxes() as $box => $val)
                              @php
                                $boxname = $val->getName();
                                $boxname = explode(".",$boxname);
                                $boxname = (count($boxname) > 1) ? $boxname[1] : "INBOX";
                                $box = $names[$boxname];
                              @endphp
                              <option value="{{$boxname}}">{{$box}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-2 col-sm-12">
                          <select class="form-control accion">
                            <option value="0">Acción</option>
                            <option value="1">Marcar como leidos</option>
                            <option value="2">Marcar como no leidos</option>
                            <option value="3">Eliminar</option>
                          </select>
                        </div>

                      </div>
                    </div>
                    @endif
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-3 col-sm-12">
                      <ul class="mailboxes">
                        @php
                          $box1 = (isset($_REQUEST["box"])) ? $_REQUEST["box"] : "INBOX";
                          $rebox = (!strstr($box1,"INBOX")) ? "INBOX.$box1" : $box1;
                        @endphp
                        @foreach ($con->getMailboxes() as $box => $val)
                          @php
                            $boxname = $val->getName();
                            $boxname = explode(".",$boxname);
                            $boxname = (count($boxname) > 1) ? $boxname[1] : "INBOX";
                            $box = $names[$boxname];
                            $s=$con->getMailBox($val->getName())->getMails();
                            $s->where("SEEN",FALSE);
                          @endphp

                            <li class="mailbox {{ ($rebox == $val->getName()) ? "sel" : ""}}">
                              <span class="badge badge-primary float-end">
                                {{$s->countMails()}}
                              </span>
                              <a href="?box={{$boxname}}">
                                {{$box}}
                              </a>
                            </li>
                        @endforeach
                      </ul>
                    </div>
                    @php
                      $mailbox = $con->getMailbox($rebox);
                      $selection = $mailbox->getMails();
                    @endphp
                    @if (isset($_REQUEST["mail"]))
                        @php
                          $mail = $selection[$_REQUEST["mail"]];
                          $mail->setFlags(["\\SEEN" => TRUE, "\\DRAFT" => FALSE]);
                          $con->flush();
                        @endphp
                          <div class="col-9">
                            <div class="clearfix">
                              <div class="float-start">
                                <h3>{{$mail->getHeader("subject")}}</h3>
                              </div>
                              <div class="float-end">
                                <div class="row">
                                  <div class="col-12" style="padding-right:50px;">
                                    <a href="/bandeja/correo/listar?mail={{$_REQUEST["mail"]-1}}">
                                      <div class="btn btn-primary">
                                        <
                                      </div>
                                    </a>
                                    <a href="/bandeja/nuevo/correo?box={{$rebox}}&re={{$_REQUEST["mail"]}}">
                                      <div class="btn btn-primary">
                                        Responder
                                      </div>
                                    </a>
                                    <div class="btn btn-primary">
                                      Reenviar
                                    </div>
                                    @php
                                      $correo = explode(" <",$mail->getHeader("from"));
                                      $correo = substr($correo[1],0,-1);
                                      $us = \App\User::where("email",$correo)->first();
                                    @endphp
                                    @if($us != null && $us->cliente != null && $us->cliente->credito_info != NULL)
                                      <a href="/creditos/creditos?cid={{md5($us->cliente->id)}}">
                                        <div class="btn btn-primary">
                                          Ver créditos
                                        </div>
                                      </a>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>
                            <p>De:{{$mail->getHeader("from")}}</p>
                            <p>Para:{{$mail->getHeader("to")}}</p>
                            <small>Recibido:{{\Carbon\Carbon::parse($mail->getHeader("Date"))->diffForHumans()}}</small>
                            <div class="row">
                              <div class="col-12">
                                <iframe class="frameset" width="100%" height="600px" srcdoc="{{$mail->getBody()}}"></iframe>
                              </div>
                            </div>
                            <hr>
                              @if (count($mail->getAttachments()) > 0)
                                <h6>Archivos adjuntos</h6>
                                <ul>
                                  @foreach ($mail->getAttachments() as $attach)
                                    <li>
                                      <a href="/bandeja/attach/download?mail={{$_REQUEST["mail"]}}&from={{urlencode($_REQUEST["from"])}}&box={{$box1}}&attach={{urlencode($attach->getName())}}" target="_blank">
                                        {{urldecode($attach->getName())}}
                                      </a>
                                    </li>
                                  @endforeach
                                </ul>

                              @endif
                          </div>
                      @else
                        <div class="col-md-9 col-sm-12">
                          <div class="list-group list-group-flush">
                            <div class="col-12 mail">
                              <input type="checkbox" id="all" class="all mt-3">
                            </div>
                            <form class="mails" action="#" method="post">
                              @if (count($selection->fetchAll()) > 0)
                                @php
                                  $i=0;
                                @endphp
                                @if (!isset($_REQUEST["buscar"]))
                                  @foreach ($selection->fetchAll() as $index => $mail)
                                    @if ($i++<50)
                                      @php
                                        $flags = $mail->getFlags();
                                      @endphp
                                            <div class="col-12 mail {{($flags["\SEEN"]) ? "gray" : ""}}">
                                              <div class="row">
                                                <div class="col-md-10 hi">
                                                       <input type="checkbox" class="mailboxcheck" name="mail[]" id="mail{{$index}}" value="{{$index}}">
                                                      <label for="mail{{$index}}" class="subject i15 mailevent"  from={{urlencode($mail->getHeader("from"))}} cid="{{$index}}">
                                                        {{$mail->getHeader("from")}} -
                                                      </label>
                                                      <small class="subject">
                                                        @php
                                                          $a = substr($mail->getHeader("Subject"),0,50);
                                                          $subject = strlen($a) == 0 ? "Sin asunto" : $a;
                                                        @endphp
                                                        <b>{{$subject}}</b>
                                                      </small>
                                                      @if (count($mail->getAttachments()) > 0)
                                                          <i class="fas fa-paperclip"></i>
                                                      @endif
                                                </div>
                                                <div class="col text-right date">
                                                  {{\Carbon\Carbon::parse($mail->getHeader("Date"))->diffForHumans()}}
                                                </div>
                                              </div>
                                            </div>
                                    @endif
                                  @endforeach
                                  @else
                                    @foreach ($selection->fetchAll() as $index => $mail)
                                      @php
                                        $flags = $mail->getFlags();
                                        try{
                                          $to = $mail->getHeader("to");
                                        } catch(Exception $e){
                                          $to = "";
                                        }
                                      @endphp
                                      @if ((strstr(strtolower($mail->getHeader("Subject")),strtolower($_REQUEST["buscar"])) || strstr(strtolower($mail->getHeader("From")),strtolower($_REQUEST["buscar"])) || strstr(strtolower($to),strtolower($_REQUEST["buscar"]))))
                                        <div class="col-12 mail {{($flags["\SEEN"]) ? "gray" : ""}}">
                                          <div class="row">
                                            <div class="col-10">
                                                <input type="checkbox" class="mailboxcheck" name="mail[]" id="mail{{$index}}" value="{{$index}}">
                                                  <label for="mail{{$index}}" class="subject i15 mailevent"  from={{urlencode($mail->getHeader("from"))}} cid="{{$index}}">
                                                    {{explode("\"",$mail->getHeader("from"))[1]}} -
                                                  </label>
                                                  <small>
                                                    @php
                                                      $a = substr($mail->getHeader("Subject"),0,50);
                                                      $subject = strlen($a) == 0 ? "Sin asunto" : $a;
                                                    @endphp
                                                    <b>{{$subject}}</b>
                                                  </small>
                                                  @if (count($mail->getAttachments()) > 0)
                                                      <i class="fas fa-paperclip"></i>
                                                  @endif
                                            </div>
                                            <div class="col text-right right">
                                              {{\Carbon\Carbon::parse($mail->getHeader("Date"))->diffForHumans()}}
                                            </div>
                                          </div>
                                        </div>
                                      @endif
                                    @endforeach
                                @endif
                                @else
                                  No hay mensajes
                              @endif
                            </form>
                          </div>
                        </div>
                    @endif
                  </div>

                  <?php
                    } catch (\Exception $e) {
                      ?>
                      {{$e}}
                        <div class="col">

                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                          <form class="" action="/user/seto" method="post">
                            @csrf
                            <label for="">Introduce tu contraseña:</label>
                            <input type="text" class="form-control" name="codigo" placeholder="Clave de acceso" value="{{Auth::user()->codigo2}}">
                            <hr>
                            <input type="submit" class="btn btn-primary" value="Cambiar clave">
                          </form>
                        </div>
                        <div class="col">

                        </div>
                      <?php
                    }
                  ?>
                </div>
            </div>
        </div>
@endsection
@section('styles')
  <style media="screen">
    .mail{
      padding-left: 10px;
    }
    .hi{
      overflow-x: auto;
      overflow: hidden;
      white-space: nowrap;
      width:70%;
    }
    .date{
      overflow-x: auto;
      overflow: hidden;
      white-space: nowrap;
      width:20%;
    }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".mailevent").bind("click",function(){
      location.href = "?mail="+$(this).attr("cid")+"&box={{$box1}}&from=";
    });
    $(function(){
      $(".all").bind("click",function(){
        $(".mailboxcheck").click();
      });
      $(".accion").on('change',function(){
        $(".mails").attr("action","/bandeja/accion?box={{$rebox}}&accion="+$(this).val());
        $(".mails").submit();
      });
      $(".mover").on('change',function(){
        $(".mails").attr("action","/bandeja/mover?box={{$rebox}}&mover="+$(this).val());
        $(".mails").submit();
      });
    });
  </script>
@endsection
