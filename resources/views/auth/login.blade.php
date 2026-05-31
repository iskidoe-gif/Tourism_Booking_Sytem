<x-layout>
    <div class="panel">
        <h1 class="title">
            {{ ($role ?? 'tourist') === 'admin' ? 'Admin Login' : 'Tourist Login' }}
        </h1>
        <p class="lead">
            {{ ($role ?? 'tourist') === 'admin' ? 'Please enter your admin credentials to access the dashboard.' : 'Please enter your email and password to log in, or continue as a guest.' }}
        </p>

        <form class="form" method="POST" action="{{ ($role ?? 'tourist') === 'admin' ? route('admin.login.store') : route('login.store') }}">
            @csrf
            <div class="group">
                <label class="label">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" class="input" />
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="group">
                <label class="label">Password</label>
                <input name="password" type="password" class="input" />
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>
            <button class="primary">Login</button>
        </form>

        @if(($role ?? 'tourist') !== 'admin')
            <form class="form" method="POST" action="{{ route('guest.login') }}">
                @csrf
                <button class="secondary">
                    Continue as Guest
                </button>
            </form>
        @endif
    </div>
</x-layout>
