<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>UNISANT ORIZABA</title>
    <style media="screen">
      @import url("https://use.typekit.net/wsn4vrj.css");
      a{
        color:#CD2C22;
        text–decoration:none;
      }
      .footer{
        background:#333;
        height:50px;
        text-align:center;
        color:white;
        font-size:3mm;
      }
      .borde{
        width:80%;
        margin-left:auto;
        margin-right:auto;
      }
      .padding{
        padding:10px;
      }
      .contenido{
        padding:30px;
      }
      .left{
        text-indent:15px;
      }
    </style>
	</head>
	<body>
		@yield("content")
		<table align="center" class="borde" cellpadding="0" cellspacing="0">
			<td class="footer">
        <p>Correo generado automaticamente, no responder.</p>
         <a href="http://{{$_SERVER['HTTP_HOST']}}"> Unisant Orizaba</a> {{date("Y")}}
			</td>
			</tr>
		</table>
	</body>
</html>
