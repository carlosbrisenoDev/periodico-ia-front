$(document).ready(function() {
    let rmenu = document.createElement("div");
    rmenu.setAttribute("id","rmenu");
    rmenu.classList = "hide2";
    document.body.append(rmenu);

    let dlink = document.createElement("a");
    dlink.setAttribute("href","/");
    dlink.innerText = "Inicio";
    let inside = document.createElement("i");
    inside.classList = "iconito fa-solid fa-caret-right";
    dlink.prepend(inside);
    dlink.classList = ("list-group-item list-group-item-action");
    rmenu.append(dlink);

    $('body').on('contextmenu', function() {
      $(".optionallink").remove();
      document.getElementById("rmenu").className = "hide2";
      let x = event.clientX;
      let y = event.clientY;
      let element = document.elementFromPoint(x,y);
  
      if($(element).attr("type")!="text"){
        document.getElementById("rmenu").className = "show2 list-group";
        document.getElementById("rmenu").style.top = event.pageY-25 + 'px';
        document.getElementById("rmenu").style.left = event.pageX + 'px';
        window.event.returnValue = false;
      }
      if($(element).attr("route") != undefined){
        var route = $(element).attr("route");
        var text = $(element).attr("route_name");
        if(route.trim() != ""){
          var spliter = route.indexOf("javascript:") != -1 ? "|" : ",";
          $.each(route.split(spliter),(i,e) => {
            var link = $("<a>").attr("href",route.split(spliter)[i]).text(text.split(spliter)[i]);
            link.prepend($("<i>").addClass("iconito fa-solid fa-caret-right"));
            link.addClass("list-group-item list-group-item-action optionallink");
            $("#rmenu").append(link);
          });
        }
      }
      console.log(element);
    });
  });
  
  // this is from another SO post...
  $(document).bind("click", function(event) {
    document.getElementById("rmenu").className = "hide2";
  });
  