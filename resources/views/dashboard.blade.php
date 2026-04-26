<x-app-layout>
    <style>
        body { overflow-x: hidden !important; overflow-y: overlay; }
        .min-h-screen { min-height: 100vh !important; display: flex; flex-direction: column; }
        .max-w-7xl { max-width: 98% !important; }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-2">
            <h2 class="font-black text-xl text-gray-800 leading-tight tracking-tighter uppercase">
                Aquatec <span class="text-blue-600 px-1">•</span> Dashboard
            </h2>
            
            {{-- REGLA OPERADOR/JEFE/ADMIN: Solo ellos crean OTs --}}
            @if(in_array(auth()->user()->role, ['admin', 'jefe_tecnico', 'operador']))
                <a href="{{ route('orden_trabajos.create') }}" 
                   class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-black text-[11px] uppercase tracking-wider transition-all shadow-[0_4px_0_rgb(30,64,175)] active:shadow-none active:translate-y-1">
                    + Nueva Orden
                </a>
            @endif
        </div>
    </x-slot>

    <div class="flex-1 py-6 sm:py-8 bg-[#f1f5f9]"> 
        <div class="px-4 sm:px-6 lg:px-8 h-full">
            
            {{-- REGLA TARJETAS: Solo Jefe Técnico y Administrador las ven --}}
            @if(in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <p class="text-gray-400 text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Total</p>
                        <p class="text-slate-900 text-2xl sm:text-4xl font-black">{{ $ots->count() }}</p>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border-l-4 sm:border-l-8 border-blue-600 shadow-sm border-y border-r border-gray-200">
                        <p class="text-blue-600/60 text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Pendientes</p>
                        <p class="text-blue-600 text-2xl sm:text-4xl font-black">{{ $ots->where('estado', 'creada')->count() }}</p>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border-l-4 sm:border-l-8 border-amber-500 shadow-sm border-y border-r border-gray-200">
                        <p class="text-amber-500/60 text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">En Planificación</p>
                        <p class="text-amber-500 text-2xl sm:text-4xl font-black">{{ $ots->where('estado', 'en_planificacion')->count() }}</p>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-2xl border-l-4 sm:border-l-8 border-emerald-500 shadow-sm border-y border-r border-gray-200">
                        <p class="text-emerald-500/60 text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Activas</p>
                        <p class="text-emerald-500 text-2xl sm:text-4xl font-black">{{ $ots->where('estado', 'activa')->count() }}</p>
                    </div>
                </div>
            @endif

            {{-- TABLA DE ÓRDENES --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-white">
                    <h3 class="text-[11px] sm:text-[12px] font-black text-slate-800 uppercase tracking-[0.2em]">Órdenes de Trabajo Recientes</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[9px] sm:text-[10px] tracking-widest">
                                <th class="p-4">OT</th>
                                <th class="p-4 text-left">Cliente / Unidad</th>
                                <th class="p-4 text-left">Lugar</th>
                                <th class="p-4 text-left">Vigencia</th>
                                <th class="p-4 text-center">Negoc.</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($ots as $ot)
                                {{-- REGLA FILTRADO VISUAL --}}
                                @if(auth()->user()->role == 'tecnico' && ($ot->estado == 'creada' || $ot->estado == 'en_planificacion'))
                                    @continue
                                @endif

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 font-black text-blue-600 text-sm sm:text-base">
                                        {{ $ot->numero_ot }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800 text-xs sm:text-sm uppercase">{{ $ot->cliente }}</div>
                                        <div class="text-[9px] sm:text-[10px] text-gray-500 uppercase font-medium">{{ $ot->embarcacion_unidad }}</div>
                                    </td>

                                    {{-- 1. LUGAR --}}
                                    <td class="p-4 text-[10px] sm:text-xs text-gray-600 uppercase font-semibold">
                                        {{ $ot->lugar ?? '---' }}
                                    </td>

                                    {{-- 2. FECHA VIGENCIA --}}
                                    <td class="p-4 text-[10px] sm:text-xs text-gray-600">
                                        {{ $ot->permiso_fecha_vigencia ? \Carbon\Carbon::parse($ot->permiso_fecha_vigencia)->format('d/m/y') : 'S/V' }}
                                    </td>

                                    {{-- 3. NEGOCIABLE (Icono visual rápido) --}}
                                    <td class="p-4 text-center">
                                        @if($ot->permiso_negociable)
                                            <span class="text-green-500 font-black text-[10px]">SÍ</span>
                                        @else
                                            <span class="text-gray-300 font-bold text-[10px]">NO</span>
                                        @endif
                                    </td>

                                    <td class="p-4">
                                        @php
                                            $clasesEstado = [
                                                'creada' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                                'en_planificacion' => 'bg-amber-100 text-amber-800 border border-amber-300',
                                                'activa' => 'bg-blue-100 text-blue-600',
                                                'finalizada' => 'bg-green-100 text-green-600'
                                            ];
                                            $textoEstado = [
                                                'creada' => 'POR PLANIFICAR',
                                                'en_planificacion' => 'EN LOGÍSTICA',
                                                'activa' => 'EN CURSO',
                                                'finalizada' => 'LISTO'
                                            ];
                                        @endphp
                                        <span class="px-2 sm:px-3 py-1 rounded-full text-[8px] sm:text-[9px] font-black uppercase {{ $clasesEstado[$ot->estado] ?? 'bg-gray-100' }}">
                                            {{ $textoEstado[$ot->estado] ?? $ot->estado }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            {{-- 1. BOTÓN LOGÍSTICA/REVISAR: SOLO JEFE Y ADMIN EN ESTADOS INICIALES --}}
                                            @if(in_array($ot->estado, ['creada', 'en_planificacion']) && in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                                                <a href="{{ route('orden_trabajos.edit', $ot->id) }}" 
                                                class="bg-amber-500 text-white px-3 py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black uppercase hover:bg-amber-600 transition-all shadow-sm">
                                                    {{ $ot->estado == 'creada' ? 'Logística' : 'Revisar' }}
                                                </a>
                                            @endif

                                            {{-- 2. BOTÓN TRABAJAR: SOLO PARA TÉCNICOS EN OT ACTIVAS --}}
                                            @if($ot->estado == 'activa' && auth()->user()->role == 'tecnico')
                                                <a href="{{ route('tecnico.show', $ot->id) }}" 
                                                class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black uppercase hover:bg-slate-900 transition-all shadow-sm">
                                                    Trabajar
                                                </a>
                                            @endif

                                            {{-- 3. BOTÓN VER AVANCE: SOLO JEFE Y ADMIN EN OT ACTIVAS --}}
                                            @if($ot->estado == 'activa' && in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                                                <a href="{{ route('tecnico.show', $ot->id) }}" 
                                                class="bg-slate-700 text-white px-3 py-1.5 rounded-lg text-[8px] sm:text-[10px] font-black uppercase hover:bg-slate-900 transition-all shadow-sm">
                                                    Ver Avance
                                                </a>
                                            @endif

                                            {{-- 4. BOTÓN DETALLE (LUPA): SOLO JEFE Y ADMIN --}}
                                            @if(in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                                                <a href="{{ route('orden_trabajos.show', $ot->id) }}" 
                                                class="p-1.5 sm:p-2 bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200 transition-colors"
                                                title="Ver Detalle Histórico">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>