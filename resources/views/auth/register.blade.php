<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Bolinao Tourism</title>
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
        <p class="text-white mt-1" style="opacity:.85">Create your account</p>
      </div>

      <div class="card">
        <div class="card-body p-4">

          @if($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold small">Name</label>
              <input type="text" name="name" value="{{ old('name') }}"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="Juan dela Cruz" required autofocus>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Email address</label>
              <input type="email" name="email" value="{{ old('email') }}"
                     class="form-control @error('email') is-invalid @enderror"
                     placeholder="you@example.com" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Phone (optional)</label>
              <input type="text" name="phone" value="{{ old('phone') }}"
                     class="form-control @error('phone') is-invalid @enderror"
                     placeholder="09XXXXXXXXX">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Password</label>
              <input type="password" name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="••••••••" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Register
              </button>
            </div>
          </form>
        </div>
      </div>

      <p class="text-center text-white mt-3 small">
        Already have an account?
        <a href="{{ route('login') }}" class="text-white fw-semibold">Login here</a>
      </p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
