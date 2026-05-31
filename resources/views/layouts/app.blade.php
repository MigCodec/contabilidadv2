<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Contabilidad' }}</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; color: #1f2937; background: #f4f6f8; }
        header { background: #12343b; color: #fff; padding: 16px 24px; }
        nav { display: flex; gap: 14px; align-items: center; }
        nav a { color: #fff; text-decoration: none; font-weight: 700; }
        main { max-width: 1120px; margin: 0 auto; padding: 24px; }
        .panel { background: #fff; border: 1px solid #d7dee5; border-radius: 6px; padding: 18px; margin-bottom: 18px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 14px; }
        .btn { display: inline-block; border: 0; border-radius: 5px; padding: 9px 12px; background: #0f766e; color: #fff; text-decoration: none; cursor: pointer; font-weight: 700; }
        .btn.secondary { background: #475569; }
        .btn.danger { background: #b91c1c; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #eef2f6; font-size: 13px; text-transform: uppercase; }
        label { display: block; margin-bottom: 6px; font-weight: 700; }
        input, select, textarea { width: 100%; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 5px; padding: 9px; font: inherit; background: #fff; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .stack { display: grid; gap: 14px; }
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 16px; }
        .alert.ok { background: #dcfce7; color: #14532d; }
        .alert.error { background: #fee2e2; color: #7f1d1d; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        .checkbox { display: flex; gap: 8px; align-items: center; }
        .checkbox input { width: auto; }
        .inline-form { display: inline; }
        .link-button { background: transparent; border: 0; color: #fff; cursor: pointer; font: inherit; font-weight: 700; padding: 0; }
        .pill { display: inline-block; border-radius: 999px; background: #e2e8f0; padding: 4px 8px; margin: 2px; font-size: 13px; }
        .login-box { max-width: 420px; margin: 80px auto; text-align: center; }
        .table-scroll { overflow-x: auto; }
        .subtotal td, .subtotal th { background: #f8fafc; font-weight: 700; }
        @media (max-width: 760px) {
            .grid, .toolbar { grid-template-columns: 1fr; display: grid; }
            table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <strong>Contabilidad</strong>
            @auth
                @if (auth()->user()->hasPermission('cuentas.gestionar'))
                    <a href="{{ route('cuentas.index') }}">Plan de cuentas</a>
                @endif
                @if (auth()->user()->hasPermission('asientos.gestionar'))
                    <a href="{{ route('asientos.index') }}">Asientos</a>
                @endif
                @if (auth()->user()->hasPermission('reportes.ver'))
                    <a href="{{ route('reportes.libro-mayor') }}">Libro mayor</a>
                    <a href="{{ route('reportes.balance-general') }}">Balance general</a>
                    <a href="{{ route('reportes.balance-general-extendido') }}">Balance extendido</a>
                @endif
                @if (auth()->user()->hasPermission('permisos.gestionar'))
                    <a href="{{ route('seguridad.permisos.index') }}">Seguridad</a>
                @endif
                <form class="inline-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link-button" type="submit">Salir</button>
                </form>
            @endauth
        </nav>
    </header>
    <main>
        @if (session('status'))
            <div class="alert ok">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <strong>Revise los datos ingresados.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
