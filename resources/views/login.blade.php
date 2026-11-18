<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Iniciar sesión | Tienda</title><style>
*{box-sizing:border-box}body{margin:0;background:#f3f5f7;font:16px Arial,sans-serif;color:#212529}.header{background:#212529;color:#fff;padding:22px;font-size:22px;font-weight:bold}.login{max-width:420px;margin:64px auto;padding:28px;background:white;border:1px solid #dee2e6;border-radius:6px}h1{font-size:24px;margin:0 0 24px}label{display:block;font-weight:bold;margin:16px 0 6px}input{width:100%;font:inherit;padding:10px;border:1px solid #adb5bd;border-radius:4px}button{width:100%;background:#0d6efd;color:white;border:0;border-radius:4px;padding:11px;font:inherit;margin-top:24px;cursor:pointer}.error{background:#f8d7da;color:#842029;padding:12px;border-radius:4px}@media(max-width:480px){.login{margin:30px 16px}}
</style></head><body><header class="header">Tienda</header><main class="login"><h1>Iniciar sesión</h1>
@if($errors->any())<div class="error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="post" action="{{ route('login.store') }}">@csrf
<label for="identificador">Usuario o correo electrónico</label><input id="identificador" name="identificador" value="{{ old('identificador') }}" maxlength="255" autocomplete="username" required autofocus>
<label for="password">Contraseña</label><input id="password" name="password" type="password" autocomplete="current-password" required>
<button type="submit">Entrar</button></form></main></body></html>
