<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>CRUD de Producto</title><style>
*{box-sizing:border-box}body{font:16px Arial,sans-serif;color:#212529;margin:0}.container{width:90%;max-width:1100px;margin:28px auto}.btn,button{border:0;border-radius:4px;padding:10px 15px;font:inherit;text-decoration:none;display:inline-block;cursor:pointer}.primary{background:#0d6efd;color:white}.secondary{background:#198754;color:white}.edit{background:#ffc107;color:#212529}.delete{background:#dc3545;color:white}table{width:100%;border-collapse:collapse;margin-top:12px;text-align:left}th,td{padding:12px 8px;border-bottom:1px solid #dee2e6}th{border-bottom:2px solid #dee2e6}td form{display:inline}label{display:block;font-weight:bold;margin:14px 0 6px}input,select{font:inherit;padding:9px;border:1px solid #adb5bd;border-radius:4px;width:100%;max-width:500px}.formulario{max-width:500px}.formulario button{margin-top:20px}.notice{padding:12px;background:#e7f3ff;margin:12px 0}.error{background:#f8d7da;color:#842029}.actions{white-space:nowrap}.actions>*{margin-right:8px}@media(max-width:600px){.container{width:94%}th,td{padding:10px 4px}.actions>*{margin:2px}.btn,button{padding:8px}}
</style></head><body><main class="container">
@if(session('mensaje')) <p class="notice" role="status">{{ session('mensaje') }}</p> @endif
@if(session('error')) <p class="notice error" role="alert">{{ session('error') }}</p> @endif
@if($errors->any()) <div class="notice error" role="alert"><p>Revisa los datos del formulario:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
@yield('contenido')
</main></body></html>
