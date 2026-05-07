<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="agent" content="{{auth()->user()->id}}">
    <meta name="from" content="{{auth()->user()->name}}">
    <title>wCloudChat</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script src="https://cloud.e-dav.net/socket.io/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/adebd9a11d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cloud.e-dav.net/css/master.css?r=1">
    <link rel="stylesheet" href="https://cloud.e-dav.net/css/tooltip.css?r=1">
  </head>
  <body>
    <div class="fluid-container p-0 height100">
    <div class="row no-gutters p-0 height100">
      <div class="col-auto border-right" id="barra" style="max-width:352px;">
          <div class="settings-tray">
            <div class="friend-drawer no-gutters friend-drawer--grey" style="display: block;;">
            <div class="clearfix">
              <div class="float-left">
                <div class="text">
                  <h6>{{auth()->user()->name}}</h6>
                  <p class="text-muted">
                    {{auth()->user()->levels->name}}
                  </p>
                </div>
              </div>
              <div class="float-right">
                <span class="settings-tray--right">
                  <i class="material-icons" data-tooltip="Recargar chats" id="reload-chats">cached</i>
                  <i class="material-icons more_menu" data-tooltip="Más" menu="menu_bar">menu</i>
                  <div class="morecontainer">
                    <div id="more" class="d-none menu_bar">
                      <ul>
                        <li>Chats archivados</li>
                        <li>Cerrar sesión</li>
                      </ul>
                    </div>
                  </div>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="search-box">
          <div class="input-wrapper">
            <i class="material-icons">search</i>
            <input placeholder="Buscar" type="text">
          </div>
        </div>
        <div class="row" id="chatsfeh">
          <div class="col-md-12">
            <span class="chat_title"><i class="fa-solid fa-expand"></i> Sin agente</span>
            <div id="chats_forclaim">
              
            </div>
          </div>
        </div>
        <div class="row" id="chatsh">
          <div class="col-md-12">
            <span class="chat_title"><i class="fa-solid fa-users-viewfinder"></i> Mis clientes</span>
            <div id="chats">

            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="settings-tray">
            <div class="clearfix">
              <div class="float-left">
                <div class="friend-drawer no-gutters friend-drawer--grey">
                <div class="profile-image" id="profile_image" style="background-color:#6f6f6f;">?</div>
                <div class="text">
                  <h6 id="nombre"> </h6>
                  <small class="text-muted" id="last_time"></small>
                </div>
                </div>
              </div>
              <div class="float-right">
                <span class="settings-tray--right">
                  <i class="material-icons" data-tooltip="Recargar chat" id="reload-chat">cached</i>
                  <i class="material-icons" id="btn_plantilla" data-tooltip="Emitir un aviso">speaker_notes</i>
                  <i class="material-icons more_menu" menu="menu_mas" data-tooltip="Más">menu</i>
                  <div class="morecontainer">
                    <div id="more" class="d-none menu_mas">
                      <ul>
                        <li>Archivar</li>
                        <li>Transferir</li>
                        <li>Notas</li>
                      </ul>
                    </div>
                  </div>
                </span>
              </div>
          </div>
        </div>
        <div class="chat-panel height100">
          <div id="messages" class="height100">

          </div>
          <div class="row" id="message-box">
            <div class="col-12">
              <div class="chat-box-tray">
                <input type="file" id="file" class="d-none">
                <div class="btn-upload" data-tooltip-up="Subir un documento ó imagen" id="btn-upload">
                  <i class="material-icons">cloud_upload</i>
                </div>
                <input type="text" id="message" placeholder="Escribe tu mensaje aquí ...">
                <div data-tooltip-up="Grabar mensaje de audio">
                  <i class="material-icons">mic</i>
                </div>
                <div data-tooltip-up="Enviar mensaje">
                  <i class="material-icons" id="send">send</i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
      var url = new URL(location.href);
      let custom_url = "https://cloud.e-dav.net"
      var audio = new Audio(custom_url+'/sounds/unpop.mp3');
      let swoosh = new Audio(custom_url+'/sounds/swoosh.mp3');
      let roomid = url.searchParams.get("room");
      let agent = {
        name : $("meta[name=from]").attr("content"),
        id: document.querySelector("meta[name=agent]").getAttribute("content"),
        clients : []
      }
      let resources = ["[image]","[audio]","[document]","[sticker]","[video]","[contacts]"];
      let config = {
        isNewLine : false
      }
      var socket = io.connect('cloud.e-dav.net', {query: 'roomid='+roomid+"&agent="+agent.id});

      $(() => {
          $("#send").click(()=>{
              sender();
          });
          $("#message").keyup(()=>{
              if(event.keyCode == 13){
                sender();
              }
          })
          $("#reload-chats").click(()=>{
              $("#chats").empty();
              $("#chats").html("<br><br><i class='fa fa-gear fa-spin'></i>");
              getChats();
          })
          $("#reload-chat").click(()=>{
              $("#messages").empty();
              $("#messages").html("<br><br><i class='fa fa-gear fa-spin'></i>");
              getMessages();
          })
          $("#btn_plantilla").click(()=>{
              sendPlantilla()
          })
          $("#btn-upload").click(()=>{
              $("#file").click();
          })
          $(".more_menu").click(()=>{
              let me = event.target
              if($(`.${$(me).attr("menu")}`).hasClass("d-none")){
                $(`.${$(me).attr("menu")}`).removeClass("d-none")
              } else {
                $(`.${$(me).attr("menu")}`).addClass("d-none")
              }
          })
          $("#file").change(() => {
            Swal.fire({
              "title" : "¿Subir?",
              "icon" : "question",
              "text" : "Sí estas seguro que deseas subir y enviar el archivo: "+$("#file").val(),
              "showCancelButton" : true,
              "showConfirmButton" : true,
              "confirmButtonText" : "Subir y enviar",
              "cancelButtonText ": "Cancelar",
              preConfirm: () => {
                Swal.showLoading()
                var formData = new FormData();
                formData.append("fileupload", document.querySelector("#file").files[0]);
                formData.append("roomid", roomid);
                formData.append("name", agent.name);
                formData.append("from", agent.name);
                return fetch(`${custom_url}/upload`, {
                  method: 'POST',
                  body: formData
                })
                  .then(response => {
                    if (!response.ok) {
                      throw new Error(response.statusText)
                    }
                    return response.text()
                  })
                  .catch(error => {
                    Swal.showValidationMessage(
                      `Request failed: ${error}`
                    )
                  })
              },
              allowOutsideClick: () => !Swal.isLoading()
            })
          });
          getChats()
          getChatsForClaim()
          getMessages()

      })
      $("#messages").css({"height":window.innerHeight-150});
      socket.on('message', addUnique)
      socket.on("client_message",updateChatRect)
      socket.on("takenchat",takenChat)
      socket.on("status_update",(data) => setStatus(data.waid,data.status))
      socket.on("newchat",body => {
        let uchat = {
          _id : body.roomid,
          all : [
            {
              name : body.name, 
              last : body.message,
              status : body.status,
              last_time: new Date(),
            }
          ],
          count : 1
        }
        addChat(uchat,"#chats_forclaim")
      })
      function takenChat(body){
        if(location.href.indexOf(body.roomid) != -1){
          let code = $(`#chat_${body.roomid}`).html();
          $(`#chat_${body.roomid}`).remove()
          $("#chats").prepend(`<div class="friend-drawer friend-drawer--onhover" onclick="selectChat('${body.roomid}')" id="chat_${body.roomid}">${code}</div>`)
        } else {
          $(`#chat_${body.roomid}`).remove()
        }
        swoosh.play();
      }
      function addUnique(message){
        audio.play(); 
        addMessages(message);
      }

      function updateChatRect(message){
        audio.play();
        console.log("Mensaje nuevo",message)
        updateBar(message)
      }
      
      function sender(){
        if(agent.clients[roomid]){
          sendMessage({agentid:agent.id,name:agent.name,from:agent.name,roomid:roomid, message: $("#message").val()});
              $("#message").focus();
              $("#message").val("");
        } else {
          Swal.fire({
            "title" : "!Ups!, Muy tarde",
            "text" : "Ya han pasado más de 24 Horas desde el último mensaje de este cliente, ya no podrás contactarlo por aquí a menos que el cliente te escriba nuevamente o envies una plantilla de seguimiento.",
            "icon" : "warning",
            "showCancelButton" : true,
            "showConfirmButton" : true,
            "showDenyButton" : true,
            "confirmButtonText" : "Enviar una plantilla",
            "denyButtonText" : "Entiendo",
            "cancelButtonText ": "Cancelar"
          }).then(result => {
            console.log(result)
            if(result.isConfirmed){
              sendPlantilla()
            }
          })
        }
      }
      function addMessages(message){
          if(message.read == 0 && config.isNewLine == false){
            config.isNewLine = true
            $("#messages").append(`<div class="separator">Nuevos mensajes</div>`)
          }
          $("#messages").append(`
            <div class="clearfix">
              <div class="${message.from == "user" ? "float-left" : "float-right fromhere"}">
                <div class="chat-bubble chat-bubble--${message.from == "user" ? "left" : "right"}">
                  <div class="clearfix">
                    <div class="float-left">
                      <small>${message.name}</small>
                    </div>
                    <div class="float-right">
                      <small>${timeForHumans(message.created)}</small>
                    </div>
                  </div>
                  <span>
                    ${message.mime && message.type == "document" ? `<div class='document-chat'><a download href='${custom_url}/resources/${message.image}.${message.mime.split("/")[1]}'><i class="fa fa-file fa-3x"></i></a></div></br>` : ""}
                    ${message.mime && message.type == "image" ? `<img onclick="openLink(this)" class='image-chat' onerror="loadError(this)" src='${custom_url}/resources/${message.image}.${message.mime.split("/")[1]}' /></br>` : ""}
                    ${message.mime && message.type == "sticker" ? `<img class='stiker-chat' onerror="loadError(this)" src='${custom_url}/resources/${message.image}.${message.mime.split("/")[1]}' /></br>` : ""}
                    ${message.mime && message.type == "audio" ? `<audio class="audio-chat" controls><source src="${custom_url}/resources/${message.image}.${message.mime.split("/")[1]}" type="${message.mime}"></audio></br>` : ""}
                    ${message.mime && message.type == "video" ? `<video class="video-chat" controls><source src="${custom_url}/resources/${message.image}.${message.mime.split("/")[1]}" type="${message.mime}"></video></br>` : ""}
                    ${message.mime && message.type == "contacts" ? getContact(message.image) : ""}
                    ${resources.find(m => m == message.message) ? "" : message.message}
                  </span>
                </div>
              </div>
            </div>
          `);

          scrollBottom("#messages");
      }
      function getMessages(){
        $.get(custom_url+'/room/'+roomid, (data) => {
          $("#messages").empty()
          data.forEach(addMessages);
        })
      }

      function getChats(){
        $.get(`${custom_url}/chats/unreads/${agent.id}`, (data) => {
          console.log(data);
          data.sort((b, a) => a.all[0].last_time.localeCompare(b.all[0].last_time))
          $("#chats").empty();
          data.forEach(m => addChat(m,"#chats"));
          setTimeout(() => {
            $("#chats").css({height:$(window).height()-$("#chatsfeh").height()-$(".settings-tray").height() - $(".search-box").height() - 80})
          },1000);
        })
      }

      function getChatsForClaim(){
        $.get(`${custom_url}/chats/unreads/0`, (data) => {
          console.log(data);
          data.sort((b, a) => a.all[0].last_time.localeCompare(b.all[0].last_time))
          $("#chats_forclaim").empty();
          data.forEach(m => addChat(m,"#chats_forclaim"));
        })
      }

      function checkAvaibleToSend(message){
        let now = new Date().getTime()
        let last = new Date(message.last_time).getTime();
        let diff = (now - last) / 1000;
        diff /= (60 * 60);
        let hours = Math.abs(Math.round(diff))
        agent.clients[message.waid] = (hours < 24)
        return agent.clients[message.waid]
      }
      function timeForHumans(time){
        let fecha = time === undefined ? new Date() : new Date(time);
        let now = new Date();
        let dia = `${fecha.getFullYear()}/${fecha.getMonth()+1}/${fecha.getUTCDate()}`
        let hoy = `${now.getFullYear()}/${now.getMonth()+1}/${now.getUTCDate()}`
        let current = (dia == hoy) ? "" : dia
        return `${current} ${fecha.getHours().toString().padStart(2,0)}:${fecha.getMinutes().toString().padStart(2,0)}`
      }
      function selectChat(roomid){
        location.href = `?room=${roomid}`
        $(this).find(".counter").addClass("d-none")
        $(this).find(".counter").text(0)
      }
      function updateBar(message){
        let fecha = timeForHumans()

        $(`#chat_${message.roomid}`).find(".last").text(message.message)
        $(`#chat_${message.roomid}`).find(".time").text(fecha)
        $(`#chat_${message.roomid}`).find(".counter").removeClass("d-none")
        $(`#chat_${message.roomid}`).find(".counter").text(parseInt($(`#chat_${message.roomid}`).find(".counter").text())+1)
      }
      function addChat(uchat, where){
        let chat = {
          "waid" : uchat._id,
          "name" : uchat.all[0].name,
          "last" : uchat.all[0].last,
          "last_time" : uchat.all[0].last_time,
          "status" : uchat.all[0].status,
          "unread" : uchat.count
        }

        let enabledToSend = checkAvaibleToSend(chat)

        let fecha = timeForHumans(chat.last_time)

        let colors = ["E040FB","536DFE","7C4DFF","40C4FF","448AFF","1DE9B6","FBC02D","FFC400","FF9100"];
        let color = colors[Math.floor(Math.random() * colors.length)];

        if(chat.waid == roomid){
          $("#nombre").text(chat.name)
          $("#profile_image").text(chat.name.substring(0,1)).css({"background-color":`#${color}`})
          $("#last_time").text(fecha)
        }

        $(where).append(`
          <div id="chat_${chat.waid}" onclick='selectChat("${chat.waid}")' class="row chat-entry ${chat.waid == roomid ? "active-bar" : ""}">
            <div class="profile-chat" style="background-color:#${color};">${chat.name.substring(0,1)}</div>
            <div class="col">
              <div class="content-chat">
                <div class="clearfix">
                  <div class="float-left">
                      <h6 style="display:inline-block;" class="name">${chat.name}</h6>
                      <p class="text-muted last">${chat.last ?? "No encontrado"}</p>
                    </div>
                    <div class="float-right text-right">
                      <span data-tooltip="Mensajes sin leer" class="badge bg-danger text-white counter d-none">0</span>
                      ${!enabledToSend ? `<div data-tooltip="Han pasado más de 24 horas desde el último mensaje."><i class="fa-regular fa-clock"></i></div>` : ""}
                    </div>
                  </div>
                </div>
                <div class="clearfix">
                  <div id="status_${chat.waid}" class="float-left">
                  </div>
                  <div class="float-right text-right">
                    <span class="time text-muted small time">${fecha}</span>
                  </div>  
                </div>
            </div>
          </div>
        `);
        setStatus(chat.waid,chat.status)
        showEnableToSend(roomid)
        if(parseInt(chat.unread) >  0){
          $(`#chat_${chat.waid}`).find(".counter").removeClass("d-none")
          $(`#chat_${chat.waid}`).find(".counter").text(chat.unread)
        }
      }
      function showEnableToSend(roomid){
        if(agent.clients[roomid]){
          $("#message-box").removeClass("notsend");
          $("#message").focus();
          $("#message").val("");
        } else {
          $("#message-box").addClass("notsend");
        }
      }
      function setStatus(roomid,status){
        let checks = `<div data-tooltip="Enviando"><i class="fa-regular fa-clock"></i></div>`;
        if(status == 1){
          checks = `<div data-tooltip="Enviado"><i class="fa-regular fa-paper-plane"></i></div>`
        } else if(status == 2){
          checks = `<div data-tooltip="Entregado"><i class="fa-solid fa-check"></i><i class="fa-solid fa-check"></i></div>`
        } else if(status == 3) {
          checks = `<div data-tooltip="Entregado y leído"><i class="text-success fa-solid fa-check"></i><i class="fa-solid fa-check text-success"></i></div>`
        } else if(status == -1) {
          checks = `<div data-tooltip="Nuevo mensaje/lead"><i class="fa-solid fa-leaf text-success"></i><i class="fa-solid fa-leaf text-success"></i></div>`
        }
        $(`#status_${roomid}`).empty()
        $(`#status_${roomid}`).append(checks)
      }
      function sendMessage(message){
        $.post(custom_url+'/messages', message)
      }
      function setRoom(){
        console.log(roomid);
        $.get(custom_url+'/setroom/'+roomid);
      }
      function scrollBottom(query){
        document.querySelector(query).scrollTop = document.querySelector(query).scrollHeight
      }
      function sendPlantilla(){
        let vars = {
          client_name : "estimado postulante",
          agent_name : agent.name
        }

        let options = [
          {
            "value" : "gracias_por_comunicarte",
            "name" : "(Bienvenida) Gracias por comunicarte",
            "variable" : "agent_name",
            "texto" : "Hola, gracias por comunicarte con nosotros, mi nombre es * y seré tu asesor educativo, juntos buscaremos el plan educativo que más te pueda interesar, para comenzar, ¿cómo podemos ayudarte?."
          },
          {
            "value" : "mensaje_de_seguimiento_1",
            "name" : "(Seguimiento) No te has puesto en contacto en un tiempo",
            "variable" : "client_name",
            "texto" : `Hola **, veo que no te has puesto en contacto con nosotros desde hace algún tiempo. ¿Aún estás interesado/a en nuestro servicio educativo?

                        Espero que podamos conversar un poco más acerca de cómo podemos encontrar el plan educativo que más te agrade, recuerda que siempre estaremos aquí para resolver tus dudas.

                        ¡Saludos!`
          },
          {
            "value" : "mensaje_de_seguimiento_2",
            "name" : "(Seguimiento) No hemos hablado",
            "variable" : "client_name",
            "texto" : `¡Uy *! Veo que no hemos hablado en algún tiempo.

                      ¿Aún tienes dudas sobre nuestro servicio educativo? quisiera saber si podemos conversar un poco más y así ayudarte en todo lo que necesites.`
          },
        ]

        let inputOptions = {}
        let inputVars = {}
        let inputText = {}
        options.forEach(op => {inputOptions[op["value"]] = op["name"]})
        options.forEach(op => {inputVars[op["value"]] = op["variable"]})
        options.forEach(op => {inputText[op["value"]] = op["texto"]})

        Swal.fire({
          "title" : "Emitir aviso",
          "icon" : "info",
          "text" : "Los mensajes emitidos mediante aviso pueden enviarse aún despues de 24 horas; sí el cliente responde al mensaje emitido, podrás responder con normalidad a la conversación.",
          "input" : "select",
          inputOptions,
          "showCancelButton" : true,
          "showConfirmButton" : true,
          "confirmButtonText" : "Enviar",
          "cancelButtonText ": "Cancelar",
          preConfirm : val => {
            $.post(custom_url+'/sendplantilla',{
              roomid, 
              message:inputText[val],
              plantilla : val,
              text1 : vars[inputVars[val]],
              name:agent.name,
              from:agent.name,
              agentid : agent.id
            });
          }
        })
      }
      function getContact(data){
        let contact = JSON.parse(data)
        return `<div class="card card-body contact-chat">
            <div class="profile-img-chat">
              <i class="fa fa-user fa-2x text-white"></i>
            </div>
            <div class="profile-texto-chat">
              ${contact[0].profile.name}
            </div>
            <hr>
            <button class="btn btn-primary mt-3" onclick='createContact("${contact[0].profile.wa_id}")'>
              Crear conversación
            </button>
          </div>`
      }
      function openLink(me){
        window.open($(me).attr("src"));
      }
      function loadError(me){
        $(me).removeAttr("onerror");
        $(me).attr("src",custom_url+"/img/not_found.png");
      }
      function loadImage(img,src){
        let me = $(img);
        setTimeout(() => {
          me.attr("src",src);
        },3000);
      }
  </script>
  </body>
</html>
