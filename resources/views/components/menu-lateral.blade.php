@php($nav = $appSettings->nav())

<aside {{ $attributes->merge(['class' => 'hidden md:flex md:flex-col md:w-64 shrink-0 '.$nav['fundo'].' '.$nav['texto']]) }}>
    <div class="h-16 flex items-center gap-3 px-5 border-b {{ $nav['borda'] }}">
        @if($appSettings->logoUrl())
            <img src="{{ $appSettings->logoUrl() }}" alt="" class="h-9 max-w-32 object-contain">
        @endif

        <a href="{{ route('dashboard') }}" class="font-bold text-lg truncate" title="{{ $appSettings->nome() }}">
            {{ $appSettings->nome() }}
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <x-menu-item :href="route('dashboard')" :icone="$appSettings->icone('dashboard')" :active="request()->routeIs('dashboard')">
            Dashboard
        </x-menu-item>

        @auth
            @if(auth()->user()->isAdmin() || auth()->user()->isMedico())
                <x-menu-item :href="route('exams.index')" :icone="$appSettings->icone('exams')" :active="request()->routeIs('exams.*')">
                    Exames
                </x-menu-item>
                <x-menu-item :href="route('health.index')" :icone="$appSettings->icone('health')" :active="request()->routeIs('health.*')">
                    Histórico de Saúde
                </x-menu-item>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isTecnico())
                <x-menu-item :href="route('risks.index')" :icone="$appSettings->icone('risks')" :active="request()->routeIs('risks.*')">
                    Riscos
                </x-menu-item>
                <x-menu-item :href="route('incidents.index')" :icone="$appSettings->icone('incidents')" :active="request()->routeIs('incidents.*')">
                    Ocorrências
                </x-menu-item>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isMedico() || auth()->user()->isTecnico())
                <x-menu-item :href="route('reports.index')" :icone="$appSettings->icone('reports')" :active="request()->routeIs('reports.*')">
                    Relatórios
                </x-menu-item>
            @endif

            @if(auth()->user()->isAdmin())
                <x-menu-item :href="route('users.index')" :icone="$appSettings->icone('users')" :active="request()->routeIs('users.*')">
                    Usuários
                </x-menu-item>
                <x-menu-item :href="route('audits.index')" :icone="$appSettings->icone('audits')" :active="request()->routeIs('audits.*')">
                    Auditoria
                </x-menu-item>
            @endif
        @endauth
    </nav>

    @auth
        <div class="px-4 pb-4 border-t {{ $nav['borda'] }} pt-4 space-y-2">
            @if(auth()->user()->isAdmin())
                <x-menu-item :href="route('settings.edit')" :icone="$appSettings->icone('settings')" :active="request()->routeIs('settings.*')">
                    Configurações
                </x-menu-item>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-left {{ $nav['hover'] }} opacity-80 hover:opacity-100 transition">
                    <x-icon name="cadeado" class="w-5 h-5 mr-3 shrink-0" />
                    <span>Sair</span>
                </button>
            </form>
        </div>
    @endauth
</aside>
