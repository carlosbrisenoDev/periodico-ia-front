var token;
var socket;
$(function(){
  var video = document.getElementById('video');
  $(".closestatus").bind('click',function(){
    $(".alert-success").fadeOut();
  });
  $(".snap").bind('click',function(){
    document.getElementById('canvas2').getContext("2d").drawImage(document.getElementById('canvas'),0,0);
    $("#imagen").val(document.getElementById('canvas2').toDataURL("image/png"));
  });

});

$(function () {
  ion.sound({
  sounds: [
      {
          name: "button_tiny",
      }
  ],
  volume: 1,
  path: "/js/sounds/",
  preload: true
});

});

var addToast = function(img,titulo,cuando,msg,data){
  var id = "toast"+Math.floor(Math.random() * 100); ;
  $(".pops").append('<div class="toast" id="'+id+'" data-delay="10000" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header"><img src="'+img+'" width="20px" class="rounded mr-2" alt="..."><strong class="mr-auto">'+titulo+'</strong><small class="text-muted">'+cuando+'</small><button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div><div class="toast-body">'+msg+'</div></div>');
  $('#'+id).toast('show',{delay:5000});
  $('#'+id).on('hidden.bs.toast', function () {
    $('#'+id).remove();
  });
  $('#'+id).on('click', function () {
    location.href = "/marketing/messenger?conversation="+data.e.recipient.id+"&sender="+data.e.sender.id+"&token="+token;
  });
}
var addToast2 = function(titulo,cuando,msg){
  var id = "toast"+Math.floor(Math.random() * 100); ;
  $(".pops").append('<div class="toast" id="'+id+'" data-delay="10000" role="alert" aria-live="assertive" aria-atomic="true"><div class="toast-header"><strong class="mr-auto">'+titulo+'</strong><small class="text-muted">'+cuando+'</small><button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div><div class="toast-body">'+msg+'</div></div>');
  $('#'+id).toast('show',{delay:5000});
  $('#'+id).on('hidden.bs.toast', function () {
    $('#'+id).remove();
  });
}
