<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Bolinao Tourism</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0077B6 0%, #00B4D8 60%, #CAF0F8 100%);
            display: flex; align-items: center;
        }
        .card { border: none; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,.15); }
        .brand { font-size: 1.6rem; font-weight: 700; color: #0077B6; }
        .brand span { color: #F4A261; }
        .btn-primary { background: #0077B6; border-color: #0077B6; }
        .btn-primary:hover { background: #005f8e; border-color: #005f8e; }
    </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-4">

      <div class="text-center mb-4">
        <div class="brand">&#127754; Bolinao <span>Tourism</span></div>
        <p class="text-white mt-1" style="opacity:.85">Sign in to your account</p>
      </div>

      <div class="card">
        <div class="card-body p-4">

          @if(session('success'))
            <div class="alert alert-success py-2 small">{{ session('success') }}</div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold small">Email address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••" required>
              </div>
            </div>

            <div class="mb-3 form-check">
              <input type="checkbox" name="remember" class="form-check-input" id="remember">
              <label class="form-check-label small" for="remember">Remember me</label>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
              </button>
            </div>
          </form>
        </div>
      </div>

      <p class="text-center text-white mt-3 small">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-white fw-semibold">Register here</a>
      </p>

      {{-- Demo credentials --}}
      <div class="card mt-3" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3)">
        <div class="card-body py-2 px-3 small text-white">
          <strong>Demo:</strong> admin@bolinao.com / password123 &nbsp;|&nbsp; juan@example.com / password123
        </div>
      </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
