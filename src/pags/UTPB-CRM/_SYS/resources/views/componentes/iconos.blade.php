@if (count($cl->leads) > 0)
    <i class="fab fa-facebook-messenger"  data-bs-toggle="tooltip" data-bs-placement="top" title="Vinculado con Facebook"></i>
@endif
@if (strstr(mb_strtolower($cl->antecedente),"facebook"))
    <i class="fab fa-facebook" style="color:blue;"  data-bs-toggle="tooltip" data-bs-placement="top" title="Lead de Facebook"></i>
@endif
@if (strstr(mb_strtolower($cl->antecedente),"emagister"))
    <i class="fab fa-e" style="color:black;"  data-bs-toggle="tooltip" data-bs-placement="top" title="Lead de Emagister"></i>
@endif
@if (strstr(mb_strtolower($cl->antecedente),"sinapissste"))
    <i class="fab fa-s" style="color:green;"  data-bs-toggle="tooltip" data-bs-placement="top" title="Lead de SINAPISSSTE"></i>
@endif
@if (strstr(mb_strtolower($cl->antecedente),"educaedu"))
    <i class="fab fa-e" style="color:red;"  data-bs-toggle="tooltip" data-bs-placement="top" title="Lead de Educaedu"></i>
@endif
@if ($cl->inscripcion != null)
    <i class="fas fa-user-graduate" data-bs-toggle="tooltip" data-bs-placement="top" title="Tiene cuenta de inscripción"></i>
@endif
@if ($cl->isinscripcion != null)
    <i class="fas fa-table" data-bs-toggle="tooltip" data-bs-placement="top" title="Envió su formulario de inscripción"></i>
@endif
@if ($cl->xmaterias ==  1)
    <i class="fas fa-book" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno por materias"></i>
@endif
@if (count($cl->documentos) > 0)
    <i class="fas fa-file-upload" data-bs-toggle="tooltip" data-bs-placement="top" title="Envió sus documentos"></i>
@endif
@if ($cl->credito != null && $cl->credito_info != null)
    @if ($cl->credito_info->status == null)
      <i class="fas fa-credit-card" data-bs-toggle="tooltip" data-bs-placement="top" title="Tiene solicitud de crédito para enviar({{$cl->credito}}%)"></i>
    @endif
    @if ($cl->credito_info->status == "enviado")
        <i class="fas fa-box-open" data-bs-toggle="tooltip" data-bs-placement="top" title="Ha enviado su solicitud de crédito de ({{$cl->credito}}%)"></i>
    @endif
    @if ($cl->credito_info->status == "preaprobado")
        <i class="far fa-check-square" data-bs-toggle="tooltip" data-bs-placement="top" title="Tiene un crédito preaprobado de ({{$cl->credito}}%)"></i>
    @endif
@endif
@if ($cl->status >=  4)
    <i class="fas fa-check-circle" style="color:green;" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno"></i>
@endif
@if ($cl->baja != NULL)
    <i class="fas fa-toggle-on text-warning" style="color:green;" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno con baja temporal"></i>
@endif
