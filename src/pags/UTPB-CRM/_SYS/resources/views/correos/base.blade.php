<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>UNISANT ORIZABA</title>
    <style media="screen">
        @import url("https://use.typekit.net/wsn4vrj.css");

        a {
            color: #CD2C22;
            text–decoration: none;
        }

        .footer {
            background: #333;
            height: 50px;
            text-align: center;
            color: white;
            font-size: 3mm;
        }

        .borde {
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .padding {
            padding: 10px;
        }

        .contenido {
            padding: 30px;
        }

        .left {
            text-indent: 15px;
        }
    </style>
</head>
<body>
<table align="center" class="borde" cellpadding="0" cellspacing="0">
    <tr style="height:150px;background-color:white;color:#FFF;">
        <td class="padding">
            <img src="https://{{$_SERVER['HTTP_HOST']}}/images/logo.png" style="height:150px;width:auto;"
                 alt="Unisant"/>
        </td>
    </tr>
    <tr>
        <td>
            <h4 class="left">
                @yield("asunto")
            </h4>
        </td>
    </tr>
    <tr>
        <td>
            <div class="contenido">
                @yield("content")
            </div>
        </td>
    </tr>
    <tr>
        <td class="footer">
            <p>Correo generado automaticamente, no responder.</p>
            <a href="http://{{$_SERVER['HTTP_HOST']}}"> Unisant Orizaba</a> {{date("Y")}}
        </td>
    </tr>
</table>
</body>
</html>
