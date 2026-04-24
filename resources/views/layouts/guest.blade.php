<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Retail Simulator')</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"rel="stylesheet">

        <style>
            body {
                background: linear-gradient(135deg, #1e2a3a 0%, #2d4060 100%);
                min-height: 100vh;
            }
            .auth-card {
                border: none;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0,0,0,.25);
            }
            .auth-brand i { font-size: 2rem; color: #f0b429; }
        </style>
    </head>

    <body class="d-flex align-items-center justify-content-center py-5">

        <div style="width:100%; max-width:460px; padding: 0 1rem;">
            {{-- Brand --}}
            <div class="auth-brand text-center mb-4">
                <i class="bi bi-shop-window"></i>
                <h4 class="text-white mt-2 mb-0 fw-bold">Retail Simulator</h4>
                <small class="text-white-50">@yield('subtitle', 'Selamat datang')</small>
            </div>

            <div class="card auth-card">
                <div class="card-body p-4">
                    @yield('content')
                </div>
            </div>

            <div class="text-center mt-3">
                @yield('footer_link')
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
</html>
