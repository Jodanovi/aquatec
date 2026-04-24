<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Encabezado Principal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl mb-6 border border-slate-200">
                <div class="p-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest">Aquatec System</span>
                        </div>
                        <h2 class="text-3xl font-bold text-slate-800">OT: {{ $ot->numero_ot }}</h2>
                        <p class="text-slate-500 text-sm mt-1">
                            Estado: 
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $ot->estado == 'activa' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $ot->estado }}
                            </span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-200 transition">Panel General</a>
                        <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm shadow-lg hover:bg-blue-700 transition">Imprimir Reporte</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Columna de Tareas y Avances (Izquierda) --}}
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="font-black text-slate-400 uppercase text-xs tracking-widest ml-4">Avance de Ejecución</h3>
                    
                    @foreach($ot->tareas as $tarea)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                        <div class="p-6 border-b border-slate-50 flex justify-between items-center {{ $tarea->esta_completada ? 'bg-green-50/30' : 'bg-slate-50/50' }}">
                            <h4 class="font-bold text-slate-800 uppercase text-sm tracking-tight flex items-center gap-3">
                                <span class="flex h-3 w-3 rounded-full {{ $tarea->esta_completada ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                                {{ $tarea->descripcion_tarea }}
                            </h4>
                            @if($tarea->esta_completada)
                                <span class="text-[10px] font-bold text-green-600 uppercase italic">Completada</span>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            @forelse($tarea->reportes as $reporte)
                                <div class="mb-6 last:mb-0 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Técnico Responsable</p>
                                            <p class="text-sm font-bold text-slate-700">{{ $reporte->tecnico->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Fecha de Carga</p>
                                            <p class="text-sm text-slate-600">{{ $reporte->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>

                                    <p class="text-slate-600 text-sm leading-relaxed border-l-4 border-blue-200 pl-4 py-1 italic">
                                        "{{ $reporte->comentario }}"
                                    </p>

                                    @if($reporte->foto_path)
                                    <div class="grid grid-cols-4 md:grid-cols-6 gap-3 mt-4">
                                        @foreach(explode(',', $reporte->foto_path) as $foto)
                                        <a href="{{ asset('storage/' . trim($foto)) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . trim($foto)) }}" class="h-20 w-full object-cover rounded-xl border border-slate-200 group-hover:opacity-80 transition">
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-slate-400 text-xs italic">Aún no se han cargado avances para esta tarea.</p>
                                </div>
                            @endforelse

                            @if($tarea->validacion_profesional)
                            <div class="mt-8 p-5 rounded-2xl border-2 border-blue-100 bg-blue-50/50 relative overflow-hidden">
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-blue-600 p-1 rounded-md text-white">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                        <h5 class="text-[10px] font-black uppercase tracking-widest text-blue-800">Corrección Técnica Profesional</h5>
                                    </div>
                                    <p class="text-sm text-slate-700 font-bold italic leading-relaxed italic">
                                        "{{ $tarea->validacion_profesional }}"
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Sidebar de Información (Derecha) --}}
                <div class="space-y-6">
                    <h3 class="font-black text-slate-400 uppercase text-xs tracking-widest ml-4">Información General</h3>
                    
                    {{-- Tarjeta Cliente y Fechas --}}
                    <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl border border-slate-800">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-black uppercase text-blue-400 tracking-widest">Unidad / Embarcación</label>
                                <p class="text-xl font-bold text-white">{{ $ot->embarcacion_unidad }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase italic">{{ $ot->cliente }}</p>
                            </div>

                            {{-- Bloque de Fechas recuperado --}}
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                                <div>
                                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Fecha Inicio</p>
                                    <p class="text-xs font-medium text-slate-300">{{ $ot->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Fecha Cierre</p>
                                    <p class="text-xs font-medium text-slate-300">
                                        {{ $ot->fecha_finalizacion ? \Carbon\Carbon::parse($ot->fecha_finalizacion)->format('d/m/Y') : 'En curso' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BLOQUE DE FINALIZACIÓN (Aparece si está activa) --}}
                    @if($ot->estado == 'activa')
                    <div class="bg-white rounded-3xl p-6 shadow-sm border-2 border-blue-100">
                        <h4 class="font-black uppercase text-[10px] tracking-widest text-blue-600 mb-4">Cierre Administrativo</h4>
                        
                        <form action="{{ route('orden_trabajos.finalizar', $ot->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Dictamen Final del Jefe</label>
                                <textarea name="conclusion_jefe" rows="4" required
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl text-xs text-slate-700 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Escriba la conclusión técnica para bloquear esta OT...">{{ old('conclusion_jefe') }}</textarea>
                                @error('conclusion_jefe')
                                    <p class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                onclick="return confirm('¿Desea cerrar esta Orden definitivamente?')"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg transition-all">
                                Finalizar y Cerrar
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Mostrar Conclusión si ya está cerrada --}}
                    @if($ot->estado == 'finalizada')
                    <div class="bg-emerald-600 text-white rounded-3xl p-6 shadow-lg">
                        <h4 class="font-black uppercase text-[10px] tracking-widest opacity-80 mb-2">Dictamen Final</h4>
                        <p class="text-sm font-bold italic leading-relaxed">
                            "{{ $ot->conclusion_jefe }}"
                        </p>
                    </div>
                    @endif

                    {{-- Tarjeta Logística --}}
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <h4 class="font-black uppercase text-[10px] tracking-widest text-slate-400 mb-3">Instrucciones Logística</h4>
                        <p class="text-sm text-slate-600 leading-relaxed italic">
                            "{{ $ot->indicaciones->last()->indicacion ?? 'Sin instrucciones.' }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>