<!DOCTYPE html>
<html>

<head>
  <title>CCP Embeded into custom HTML</title>
  <meta charset="UTF-8">
  <script type="text/javascript" src="./lib/connect-streams-min.js"></script>
  <style>
    html,body{
      height:100%;
      margin:0;
    }
    #container-div{
      display:flex;
      height:100%;
      flex-direction: row-reverse;
    }
    .softphone{
      width:400px;
    }
    .ecm{
      width:100%;
    }
  </style>
</head>

<body>
  <div id="container-div">
    <div id="softphone" class="softphone" frameborder="0"></div>
    <iframe class="ecm" src="{{url('/')}}" frameborder="0"></iframe>
  </div>
  <script type="module" src="/scripts/index.js"></script>
</body>

</html>