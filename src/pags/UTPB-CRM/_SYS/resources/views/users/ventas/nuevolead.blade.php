@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                      <h3 class="text-center">Clientes</h3>
                      <div class="card borderless">
                        <ul class="list-group list-group-flush conversations">
                        </ul>
                      </div>
                    </div>
                    <div class="col-8 chat">
                      <div class="clearfix">
                        <div class="pull-left">
                          <h4>Chat</h4>
                        </div>
                        <div class="pull-right">
                          <div class="btn btn-primary irchat">
                            Ir al chat en Messenger
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-12 historial text-center" style="height:40px;">

                        </div>
                        <div class="col-12 chat_chat">

                        </div>
                      </div>
                      <div class="row">
                        <div class="col-10 nopadding">
                          <input type="text" placeholder="Escribe aqui" autofocus name="" value="" class="chat_text">
                        </div>
                        <div class="col-2 nopadding">
                          <input type="submit" class="chat_enviar" value="Enviar">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
  <script type="text/javascript">
      var sender = null;
      token = "EAAG66OoLq3sBAJmkz5ZAwV3ClCaLzXgjA1Jk8cDtKBJWlbZCntWH9Ty8hYymZCb3dzpQAXyZBkZAAoSXmZBhi6IKCbgUBZAiPEHSRFZBeXxeexlaLUPxIkK08Akp3n8m5pmMTsaekcT80TQGi24YLfK2Xx7yx9UmHkn94793K01gnjbEI1xY9p4A76t1oZCuUYZAUZD";
      $(".chat_text").keypress(function(e){
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            socket.emit("enviar",{recipient:sender,m:$(".chat_text").val()});
            $(".chat_chat").append($("<div>").addClass("chat_1").append($("<span>").text($(".chat_text").val())));
            $(".chat_text").val("");
            $(".chat_chat").scrollTop(function() { return this.scrollHeight; });
        }
      });
      var historial = function(t_psid){
        FB.api("/"+t_psid+"?fields=messages{message,from}&access_token="+token,function(response){
          if(response && !response.error){
            $.each(response.messages.data,function(i,e){
              var who = (sender == e.from.id) ? "chat_2" : "chat_1";
              $(".chat_chat").prepend($("<div>").addClass(who).append($("<span>").text(e.message)));
            });
            $(".chat_chat").scrollTop(function() { return this.scrollHeight; });
          }
        });
      }
      var faceinit = function(){
        FB.api(
          "/UnisantOrizaba?fields=conversations{id,senders,unread_count,link}&access_token="+token,
          function (response) {
            if (response && !response.error) {
              $.each(response.conversations.data,function(i,e){
                var rand = Math.round(Math.random() * 1000);
                var psid = e.senders.data[0].id;
                $(".conversations").append($("<li>")
                  .addClass("list-group-item d-flex justify-content-between align-items-center")
                  .text(e.senders.data[0].name)
                  .append($("<span>").addClass("badge badge-primary").text(e.unread_count))
                  .prepend($("<img>").addClass("rounded").attr("src","https://graph.facebook.com/"+e.senders.data[0].id+"/picture?width=20&height=20&access_token="+token))
                  .attr("id","chat"+rand)
                );
                if(sender == psid){
                  historial(e.id);
                  $(".irchat").on("click",function(){
                    location.href = "https://facebook.com"+e.link;
                  });
                  $("#chat"+rand).addClass("active");
                }
                $("#chat"+rand).on("click",function(){
                  location.href = "/marketing/messenger?sender="+psid+"&token="+token;
                });
              });
            } else {
              $(".conversations").append($("<div class='top20 badge badge-danger'>").text(response.error.message));
            }
          }
        );
        var url = 'https://graph.facebook.com/'+sender+'?fields=first_name,last_name,profile_pic&access_token='+token;
        $.get(url,function(ndata){
          $(".firste")
            .prepend(ndata.first_name)
            .prepend($("<img class='rounded wd'>").attr("src",ndata.profile_pic));
        });

        if(sender == null){
          $(".chat_text").attr("disabled","disabled");
        }
    }
    $(document).on("fbload",faceinit());
    var mensaje_update = function(psid,mensaje){
      var sender;
      @if (isset($_REQUEST["sender"]))
        sender = {{$_REQUEST["sender"]}};
      @endif
      if(sender == psid)
      {
        $(".chat_chat").append($("<div>").addClass("chat_2").append($("<span>").text(mensaje)));
        $(".chat_chat").scrollTop(function() { return this.scrollHeight; });
      }
    }
  </script>
@endsection
