<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Bolinao Tourism Booking System') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f8fafc] text-slate-900 antialiased">
        <header class="fixed inset-x-0 top-0 z-30 bg-white/80 backdrop-blur-xl shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                    Bolinao <span class="text-sky-600">Tourism</span>
                </a>
                <nav class="hidden items-center gap-6 md:flex">
                    <a href="#packages" class="text-sm font-medium text-slate-700 hover:text-slate-900">Packages</a>
                    <a href="{{ route('packages.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Browse tours</a>
                    @auth
                        <a href="{{ route('reservations.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">My reservations</a>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Admin</a>
                        @endif
                    @endauth
                </nav>
                <div class="hidden md:block">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                            Register
                        </a>
                    @endauth
                </div>
                <div class="md:hidden">
                    @auth
                        <a href="{{ route('reservations.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            Reservations
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8 pt-24">
            <header class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Bolinao Tourism</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">Book your Bolinao escape with one click.</h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600">Explore Bolinao's beaches, waterfalls, and island tours. Reserve locally guided packages, compare dates, and make every getaway unforgettable.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:w-[340px]">
                    <span class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Popular destination</p>
                        <p class="mt-3 text-xl font-semibold text-slate-900">Bolinao Lighthouse</p>
                    </span>
                    <span class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Start date</p>
                        <p class="mt-3 text-xl font-semibold text-slate-900">July 10</p>
                    </span>
                </div>
            </header>

            <main class="mt-16 grid gap-10 lg:grid-cols-[1.35fr_0.9fr]">
                <section class="space-y-10">
                    <div class="rounded-[2rem] bg-white p-8 shadow-[0_20px_70px_-20px_rgba(15,23,42,0.2)]">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-slate-500">Your next adventure</p>
                                <h2 class="mt-4 text-3xl font-semibold text-slate-900">Featured tour packages</h2>
                            </div>
                            <a href="#packages" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Browse packages</a>
                        </div>

                        <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                                <p class="text-sm font-semibold text-slate-600">Beach highlights</p>
                                <h3 class="mt-4 text-xl font-semibold text-slate-900">Dinagsa Shore</h3>
                                <p class="mt-4 text-sm leading-6 text-slate-600">3-day Bolinao beachfront stay with snorkeling, seaside lunches, and sundown views.</p>
                                <p class="mt-6 text-lg font-semibold text-slate-900">₱7,250</p>
                            </article>
                            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <p class="text-sm font-semibold text-slate-600">Island hopping</p>
                                <h3 class="mt-4 text-xl font-semibold text-slate-900">Bolinao Isles</h3>
                                <p class="mt-4 text-sm leading-6 text-slate-600">Full-day boat tour to islands, coral gardens, and hidden coves off Bolinao.</p>
                                <p class="mt-6 text-lg font-semibold text-slate-900">₱4,980</p>
                            </article>
                            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                                <p class="text-sm font-semibold text-slate-600">Nature discovery</p>
                                <h3 class="mt-4 text-xl font-semibold text-slate-900">Enchanted Falls</h3>
                                <p class="mt-4 text-sm leading-6 text-slate-600">Guided trek to waterfalls, beach caves, and local farms in scenic Bolinao.</p>
                                <p class="mt-6 text-lg font-semibold text-slate-900">₱3,650</p>
                            </article>
                        </div>
                    </div>

                    <div id="packages" class="rounded-[2rem] bg-slate-900 p-8 text-white shadow-[0_20px_70px_-20px_rgba(15,23,42,0.35)]">
                        <div class="grid gap-8 lg:grid-cols-2">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-slate-300">Why Bolinao</p>
                                <h2 class="mt-4 text-3xl font-semibold">Local tours, trusted guides, and island comfort.</h2>
                                <p class="mt-5 leading-7 text-slate-300">Explore Bolinao with curated itineraries for beaches, waterfalls, and cultural sightseeing — all in one easy booking flow.</p>
                            </div>
                            <div class="space-y-4 rounded-3xl border border-slate-700 bg-slate-800 p-6">
                                <div class="flex items-center gap-4 rounded-3xl bg-slate-950/70 p-4">
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-900">1</span>
                                    <div>
                                        <p class="text-sm text-slate-300">Choose a package</p>
                                        <p class="font-semibold text-white">Select from curated tours</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 rounded-3xl bg-slate-950/70 p-4">
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-900">2</span>
                                    <div>
                                        <p class="text-sm text-slate-300">Add traveler details</p>
                                        <p class="font-semibold text-white">Tell us who’s coming</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 rounded-3xl bg-slate-950/70 p-4">
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-900">3</span>
                                    <div>
                                        <p class="text-sm text-slate-300">Confirm and relax</p>
                                        <p class="font-semibold text-white">Booking made easy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-8">
                    <div class="rounded-[2rem] bg-white p-8 shadow-[0_20px_70px_-20px_rgba(15,23,42,0.2)]">
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Quick booking</p>
                        <h2 class="mt-4 text-2xl font-semibold text-slate-900">Reserve your tour</h2>
                        <form class="mt-8 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700" for="package">Package</label>
                                <select id="package" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-200">
                                    <option>Dinagsa Shore</option>
                                    <option>Bolinao Isles</option>
                                    <option>Enchanted Falls</option>
                                </select>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-slate-700" for="date">Start date</label>
                                    <input id="date" type="date" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-200" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-slate-700" for="guests">Guests</label>
                                    <input id="guests" type="number" min="1" value="2" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-200" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700" for="email">Email</label>
                                <input id="email" type="email" placeholder="you@example.com" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-200" />
                            </div>
                            <button type="button" class="inline-flex w-full items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Book now</button>
                        </form>
                    </div>

                    <div class="rounded-[2rem] bg-white p-8 shadow-[0_20px_70px_-20px_rgba(15,23,42,0.15)]">
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Traveler reviews</p>
                        <div class="mt-6 space-y-4">
                            <div class="rounded-3xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-600">“The entire booking experience was smooth and the luggage support made our trip effortless.”</p>
                                <p class="mt-4 text-sm font-semibold text-slate-900">— Maya K.</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-600">“We loved the curated itinerary and the responsive customer support.”</p>
                                <p class="mt-4 text-sm font-semibold text-slate-900">— Andre R.</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </main>
        </div>
    </body>
</html>
