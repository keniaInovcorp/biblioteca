<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Biblioteca') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;manrope:500,600,700" rel="stylesheet" />

            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-slate-800 flex flex-col">
        <header class="container mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3 text-xl font-semibold text-slate-900">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7 text-white">
                        <path fill="currentColor" d="M4.75 4A2.75 2.75 0 0 0 2 6.75v10.5A2.75 2.75 0 0 0 4.75 20h5.5c.653 0 1.25.27 1.678.705a.75.75 0 0 0 1.144 0A2.25 2.25 0 0 1 14.75 20h4.5A2.75 2.75 0 0 0 22 17.25V6.75A2.75 2.75 0 0 0 19.25 4h-5.5c-.653 0-1.25-.27-1.678-.705a.75.75 0 0 0-1.144 0A2.25 2.25 0 0 1 10.25 4zm0 1.5h5.5c.856 0 1.664-.28 2.32-.75h.18v13.5h-2c-.812 0-1.558.245-2.2.663c-.642-.418-1.388-.663-2.2-.663h-3.6zm9 0h5.5c.69 0 1.25.56 1.25 1.25v10.5c0 .69-.56 1.25-1.25 1.25h-4.5z"/>
                    </svg>
                </span>
                <span>{{ config('app.name', 'Biblioteca') }}</span>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-ghost">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex-1">
            <section class="container mx-auto px-6 pb-16">
                <div class="rounded-3xl bg-white shadow-xl border border-slate-200 px-8 py-12 lg:px-16 lg:py-16">
                    <div class="grid gap-10 lg:grid-cols-2 items-center">
                        <div class="space-y-6">
                            <span class="badge badge-accent badge-lg">Nova Experiência</span>
                            <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                                Sua <span class="text-primary">Biblioteca </span> digital.
                            </h1>
                            <p class="text-base-content/70 text-lg max-w-xl">
                                Pesquise títulos, autores e editoras com rapidez.
                            </p>
                        </div>
                        <div class="grid gap-4 bg-slate-50 rounded-2xl p-6 lg:p-8 border border-slate-200">
                            <div class="flex items-center gap-4">
                                <span class="badge badge-primary badge-lg">01</span>
                                <div>
                                    <h3 class="font-semibold text-lg">Solicite sem sair de casa</h3>
                                    <p class="text-base-content/70 text-sm">Localize o livro desejado e faça a sua requisição on-line em poucos cliques.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="badge badge-secondary badge-lg">02</span>
                                <div>
                                    <h3 class="font-semibold text-lg">Acompanhe o status</h3>
                                    <p class="text-base-content/70 text-sm">Acesse a lista de livros disponíveis para requisitar.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="badge badge-success badge-lg">03</span>
                                <div>
                                    <h3 class="font-semibold text-lg">Avisos e lembretes</h3>
                                    <p class="text-base-content/70 text-sm">Receba lembrete próximo à data de devolução do exemplar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </section>

            <section class="bg-slate-50 border-t border-slate-200">
                <div class="container mx-auto px-6 py-12 text-center space-y-5">
                    <h2 class="text-3xl lg:text-4xl font-bold">Pronto para requisitar o próximo livro?</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">
                        Se ainda não possui acesso, crie já a sua conta para solicitar exemplares.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-3">
                        <a href="{{ route('login') }}" class="btn btn-outline btn-sm btn-wide">Já tenho conta</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm btn-wide">Criar conta para requisitar</a>
                        @endif
                </div>
                    <p class="text-slate-500 text-sm max-w-xl mx-auto">
                        Ao se cadastrar você poderá reservar títulos, renovar empréstimos e manter seu histórico atualizado em um só lugar.
                    </p>
                </div>
            </section>
            </main>

        <footer class="py-6 text-center text-sm text-slate-500 bg-white">
            &copy; {{ now()->year }} {{ config('app.name', 'Biblioteca') }}. Todos os direitos reservados.
        </footer>
    </body>
</html>
