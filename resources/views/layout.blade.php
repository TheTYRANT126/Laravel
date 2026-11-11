<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('titulo', 'Tienda') | Tienda</title>
<style>
*{box-sizing:border-box}body{margin:0;color:#212529;background:#fff;font:16px Arial,sans-serif}.site-header{background:#212529;color:#fff}.header-inner{max-width:1140px;margin:auto;min-height:78px;padding:0 16px;display:flex;align-items:center;gap:30px}.brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-size:22px;font-weight:bold;white-space:nowrap}.ventas-nav{margin-left:auto}.main-nav{display:flex;align-items:center;gap:20px;flex-wrap:wrap}.main-nav a{color:#adb5bd;text-decoration:none;padding:9px 0;border-bottom:3px solid transparent}.main-nav a:hover,.main-nav a.active{color:#fff}.main-nav a.active{border-color:#0d6efd}.container{width:calc(100% - 32px);max-width:1140px;margin:28px auto}.card{border:1px solid #dee2e6;border-radius:5px;overflow:hidden}.card-header{margin:0;padding:16px 24px;background:#f7f7f7;border-bottom:1px solid #dee2e6;font-size:23px;font-weight:600}.card-body{padding:18px 24px 28px}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}.btn,button{border:0;border-radius:4px;padding:10px 15px;font:inherit;text-decoration:none;display:inline-block;cursor:pointer}.btn:focus-visible,button:focus-visible,input:focus-visible,select:focus-visible,.main-nav a:focus-visible{outline:3px solid #80b7ff;outline-offset:2px}.primary{background:#0d6efd;color:#fff}.secondary{background:#198754;color:#fff}.edit{background:#ffc107;color:#212529}.delete{background:#dc3545;color:#fff}.btn:not(.primary):not(.secondary):not(.edit){background:#e9ecef;color:#212529}.tabs{display:flex;border-bottom:1px solid #dee2e6;margin:20px 0 16px}.tabs a{color:#495057;text-decoration:none;padding:12px 16px;border:1px solid transparent;border-bottom:2px solid transparent}.tabs a.active{color:#0b5d92;border-color:#dee2e6 #dee2e6 #0d6efd;border-radius:4px 4px 0 0}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;text-align:left}th,td{padding:14px 12px;border-bottom:1px solid #dee2e6}th{border-top:1px solid #dee2e6;border-bottom:2px solid #dee2e6}tbody tr:last-child td{border-bottom:0}td form{display:inline}label{display:block;font-weight:bold;margin:14px 0 6px}input,select{font:inherit;padding:9px;border:1px solid #adb5bd;border-radius:4px;width:100%;max-width:500px}.search{max-width:290px}.formulario{max-width:500px}.formulario button{margin-top:20px}.notice{padding:12px;background:#e7f3ff;margin:0 0 14px}.error{background:#f8d7da;color:#842029}.actions{white-space:nowrap}.actions>*{margin-right:5px}.visually-hidden{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}[hidden]{display:none!important}@media(max-width:600px){.header-inner{align-items:flex-start;gap:8px;flex-direction:column;padding:16px}.main-nav{gap:16px}.ventas-nav{margin-left:0}.container{margin:18px auto}.card-header{padding:14px 16px}.card-body{padding:16px}.search{max-width:none}.toolbar>*{width:100%}.toolbar .btn{width:auto}.actions>*{margin:2px}th,td{padding:12px 8px}}
</style>
</head>
<body>
<header class="site-header"><div class="header-inner">
<a class="brand" href="{{ route('productos.index') }}">Tienda</a>
<nav class="main-nav" aria-label="Navegación principal">
<a href="{{ route('productos.index') }}" @class(['active' => request()->routeIs('productos.*')]) @if(request()->routeIs('productos.*')) aria-current="page" @endif>Productos</a>
<a href="{{ route('proveedores.index') }}" @class(['active' => request()->routeIs('proveedores.*')]) @if(request()->routeIs('proveedores.*')) aria-current="page" @endif>Proveedores</a>
<a href="{{ route('clientes.index') }}" @class(['active' => request()->routeIs('clientes.*')]) @if(request()->routeIs('clientes.*')) aria-current="page" @endif>Clientes</a>
</nav>
<nav class="main-nav ventas-nav" aria-label="Ventas"><a href="{{ route('ventas.index') }}" @class(['active' => request()->routeIs('ventas.*')]) @if(request()->routeIs('ventas.*')) aria-current="page" @endif>Ventas</a></nav>
</div></header>
<main class="container">
@if(session('mensaje')) <p class="notice" role="status">{{ session('mensaje') }}</p> @endif
@if(session('error')) <p class="notice error" role="alert">{{ session('error') }}</p> @endif
@if($errors->any()) <div class="notice error" role="alert"><p>Revisa los datos del formulario:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
<section class="card"><h1 class="card-header">@yield('titulo', 'Tienda')</h1><div class="card-body">
@yield('contenido')
</div></section>
</main>
<script>
const buscador = document.getElementById('buscar');
if (buscador) {
    const filas = [...document.querySelectorAll('tr.registro')];
    buscador.addEventListener('input', () => {
        const termino = buscador.value.trim().toLocaleLowerCase('es');
        let visibles = 0;
        filas.forEach(fila => {
            const texto = [...fila.cells].slice(0, -1).map(celda => celda.textContent).join(' ');
            fila.hidden = !texto.toLocaleLowerCase('es').includes(termino);
            if (!fila.hidden) visibles++;
        });
        document.getElementById('sin-resultados').hidden = !termino || !filas.length || visibles > 0;
    });
}
</script>
</body></html>
