let items = [
    {
      type:"menuitem",
      text:"Nombre del cliente",
      onAction: function(_){
        _e.insertContent('%NOMBRE%');
      }
    },
    {
      type:"menuitem",
      text:"Apellido paterno cliente",
      onAction: function(_){
        _e.insertContent('%APAT%');
      }
    },
    {
      type:"menuitem",
      text:"Apellido materno cliente",
      onAction: function(_){
        _e.insertContent('%AMAT%');
      }
    },
    {
      type:"menuitem",
      text:"Nombre de asesor",
      onAction: function(_){
        _e.insertContent('%NOMBREASESOR%');
      }
    },
    {
      type:"menuitem",
      text:"Cargo de asesor",
      onAction: function(_){
        _e.insertContent('%CARGOASESOR%');
      }
    },
    {
      type:"menuitem",
      text:"Télefono asesor",
      onAction: function(_){
        _e.insertContent('%TELEFONOASESOR%');
      }
    },
    {
      type:"menuitem",
      text:"Correo electrónico asesor",
      onAction: function(_){
        _e.insertContent('%CORREOASESOR%');
      }
    }
  ];
  let _e = null;
  tinymce.init(
    {
      height : "800",
      language: 'es',
      selector:'#contenido1',
      plugins: [ "image", "code", "table", "link", "media", "codesample"],
      toolbar_mode: 'floating',
      toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontsizeselect',
      toolbar2: 'media usefiles | forecolor backcolor emoticons | codesample',
      images_upload_url: '/files/uploadimage?_token='+$('meta[name="csrf-token"]').attr('content'),
      convert_urls:true,
      relative_urls:false,
      remove_script_host:false,
      setup: function(editor) {
        _e = editor;
        editor.ui.registry.addMenuButton('usefiles', {
         icon: 'embed-page',
         text: "Insertar variables",
         tooltip: 'Insertar documentos',
         fetch: function (callback) {
            callback(items);
          }
       });
     }
    }
  );
  
  $(function(){
    let uri = location.pathname.split("/");
    uri.shift();
    uri.shift();
    uri = uri.join("/")+location.search;
    ($("<div>").attr("id","filesviewer")).insertAfter("textarea");
    $("#filesviewer").append($("<h6>").text("Documentos adjuntos"));
    $("#filesviewer").css({
      "height":"auto",
      "border":"solid #d6d6d6 1px",
      "margin-left":"0px",
      "margin-right":"0px",
      "padding-left":"10px",
      "padding-right":"10px"
    }).addClass("row");
    $.post("/files/getfrompath",{
      "_token":$('meta[name="csrf-token"]').attr('content'),
      "pathname":uri
    },function(data){
      data = JSON.parse(data);
      $.each(data,function(i,e){
        $("#filesviewer").append($("<div>")
          .addClass("card ml-1 mr-1 p-3 text-center")
          .css({"width":"auto"})
          .append($("<i>")
            .attr("route_name","Eliminar")
            .attr("route","/marketing/comm_eliminaradjunto?cid="+e.md5)
            .addClass("fa fa-file fa-3x"))
          .append(e.filename)
        );
      });
    });
    //$("#contenido1").parent().append($("<div>").attr("id","filesman"));
    ($("<div>").attr("id","filesman")).insertAfter("textarea");
    $("#filesman").attr("class","dropzone dz-clickable");
    $("#filesman").css({"margin-top":"-20px"})
    let sc = $("<script>");
    let dc = $("<link>");
    sc.on("load",function(event){
      Dropzone.autoDiscover = false;
      let myDropzone = new Dropzone("#filesman", {
         url: "/files/postpath",
         params: {
           "_token":$('meta[name="csrf-token"]').attr('content'),
           "pathname":uri
         }
       });
       myDropzone.on("success",(done) => {
         myDropzone.removeFile(done);
         var res = JSON.parse(done.xhr.response);
         $("#filesviewer").append($("<div>")
           .addClass("card ml-1 mr-1 p-3 text-center")
           .css({"width":"auto"})
           .append($("<i>")
             .attr("route_name","Eliminar")
             .attr("route","/marketing/comm_eliminaradjunto?cid="+res.cid)
             .addClass("fa fa-file fa-3x"))
           .append(res.filename)
         );
       });
    });
    $("head").append(dc);
    $("head").append($("<style>").text(".dropzone{border:solid #d6d6d6 1px !important;border-top:0 !important;}"));
    dc.attr("rel","stylesheet")
      .attr("type","text/css")
      .attr("href","https://unpkg.com/dropzone@5/dist/min/dropzone.min.css");
    $("#contenido1").parent().append(sc);
    sc.attr("src","/js/dropzone.js");
  });
  