$(function(){
  var server = "https://webhook.gruposhirushi.com";
  socket = io.connect(server,{ 'forceNew': true });
  socket.on("conectado",function(data){
    token = data.token;
    $(".messenger").attr("href","/marketing/messenger?token="+token).removeClass("disabled");
    FB.api("/shirushimx?fields=unread_message_count&access_token="+token,function(response){
      if(response && !response.error){
        $(".msn").text(parseInt($(".msn").text())+parseInt(response.unread_message_count));
      }
    })
  });

  socket.on("mensaje_update",function(data){
    ion.sound.play("button_tiny");
    $(".msn").text(parseInt($(".msn").text())+parseInt(data.n));
    console.log(data);
    $.get('https://graph.facebook.com/'+data.e.sender.id+'?fields=first_name,last_name,profile_pic&access_token='+data.token,function(ndata){
      addToast(ndata.profile_pic,ndata.first_name,"Justo ahora",data.e.message.text,data);
    });
    mensaje_update(data.e.sender.id,data.e.message.text);
  });

});
