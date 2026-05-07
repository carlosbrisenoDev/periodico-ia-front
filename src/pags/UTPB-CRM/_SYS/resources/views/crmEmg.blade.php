<html lang="es" slick-uniqueid="3"><head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="bluWorks">
	<meta name="description" content="Incrementa — Seguimiento simplificado de ventas">

	<title>API Emg CRM — Widgets</title>

	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<body cz-shortcut-listen="true">
        <form action="{{url('/api/client/emg/web-register')}}/57a0caf58c7cbda315f5def24f899d12" method="post" class="container" id="ClientWebFormForm" accept-charset="utf-8">
            <div style="display:none;">
                <input type="hidden" name="_method" value="get">
            </div>
            <div class="form-group mt-2">
                <label for="ClientFirstName">Nombre(s)</label>
                <input name="name" class="form-control" placeholder="Nombre(s)" type="text" id="ClientFirstName" required>
            </div>
            <div class="form-group mt-2">
                <label for="ClientLastName">Apellidos</label>
                <input name="lastname" class="form-control" placeholder="Apellidos" type="text" id="ClientLastName" required>
            </div>
            <div class="form-group mt-2">
                <label for="ClientCellphone">Celular</label>
                <input name="phone" class="form-control" placeholder="Celular" type="text" id="ClientCellphone" required>
            </div>
            <div class="form-group mt-2">
                <label for="ClientEmail">Email</label>
                <input name="email" class="form-control" placeholder="Email" type="email" id="ClientEmail" required>
            </div>
            <div class="form-group mt-2">
                <label for="ClientCurso">Curso</label>
                <select name="course" class="form-control" placeholder="Curso" id="ClientCurso" required>
                    @php
                        $courses = \App\productos::where('deleted_at',null)->get();
                    @endphp
                    @foreach($courses as $c)
                    <option value="{{$c->nombre}}">{{$c->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mt-2">
                <label for="ClientNotes">Notas</label>
                <textarea name="note" class="form-control" placeholder="Notas" cols="30" rows="6" id="ClientNotes"></textarea>
            </div>
            <div class="submit">
                <input class="btn btn-light mt-2" type="submit" value="Enviar">
            </div>
        </form>	
            
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>