<x-layout>
    <div class="panel">
        <h1 class="title">Register</h1>
        <p class="lead">Create a tourist account.</p>

        <form class="form" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="group">
                <label class="label">Name</label>
                <input name="name" type="text" value="{{ old('name') }}" class="input" />
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>
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
            <div class="group">
                <label class="label">Confirm Password</label>
                <input name="password_confirmation" type="password" class="input" />
            </div>
            <button class="primary">Create account</button>
        </form>
    </div>
</x-layout>
