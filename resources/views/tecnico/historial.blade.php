<x-app-layout>
    <div class="bg-slate-50 min-h-screen pb-20">
        {{-- Cabecera con Buscador --}}
        <div class="bg-slate-900 text-white p-6 shadow-lg sticky top-0 z-50">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center gap-4 mb-6">
                    {{-- ID para el paso de Regresar --}}
                    <a id="tour-regresar" href="javascript:history.back()" class="text-blue-400 p-2 hover:bg-slate-800 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-[10px] font-black uppercase tracking-widest text-blue-400">Historial de Unidad</h1>
                        <p class="text-xl font-bold italic text-white">{{ $unidad }}</p>
                        <p class="text-[11px] font-black text-slate-400 uppercase italic">Cliente: {{ $cliente }}</p>
                    </div>
                </div>

                {{-- Buscador en tiempo real (ID: busquedaTarea) --}}
                <div class="relative">
                    <input type="text" id="busquedaTarea" placeholder="Buscar tarea (ej: Generador, Motor...)" 
                        class="w-full bg-slate-800 border-none rounded-2xl py-3 pl-10 pr-4 text-xs font-bold text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto p-4 space-y-6" id="contenedorTareas">
            @forelse($historial as $ot)
                @foreach($ot->tareas as $tarea)
                    {{-- ID DINÁMICO PARA EL TOUR: Solo se asigna a la primera tarea del primer bucle --}}
                    <div class="card-tarea bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-4 transition-all" 
                         @if($loop->parent->first && $loop->first) id="tour-primera-tarea" @endif
                         data-nombre="{{ strtolower($tarea->descripcion_tarea) }}">
                        
                        {{-- Info de la OT arriba de la tarea --}}
                        <div class="bg-slate-50 px-5 py-3 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                                OT: {{ $ot->numero_ot }} • {{ $ot->fecha_finalizacion ? $ot->fecha_finalizacion->format('d/m/Y') : $ot->updated_at->format('d/m/Y') }}
                            </span>
                            <span class="bg-emerald-100 text-emerald-700 text-[8px] font-black px-2 py-0.5 rounded uppercase">Finalizada</span>
                        </div>

                        <div class="p-5">
                            <h4 class="text-sm font-black text-slate-800 uppercase italic border-l-4 border-blue-500 pl-3 mb-4">
                                {{ $tarea->descripcion_tarea }}
                            </h4>
                            
                            {{-- Reportes de esa tarea --}}
                            <div class="space-y-3">
                                @foreach($tarea->reportes as $reporte)
                                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100">
                                        <div class="flex justify-between items-start mb-2">
                                            <p class="text-[10px] font-black text-blue-600 uppercase">{{ $reporte->tecnico->name }} escribió:</p>
                                            <span class="text-[9px] text-slate-400 font-bold">{{ $reporte->created_at->format('d/m/y') }}</span>
                                        </div>
                                        
                                        <p class="text-slate-600 text-xs italic leading-relaxed {{ $reporte->foto_path ? 'mb-3' : '' }}">
                                            "{{ $reporte->comentario }}"
                                        </p>
                                        
                                        @if($reporte->foto_path)
                                            {{-- ID DINÁMICO PARA EL TOUR: Solo al primer botón de galería --}}
                                            <button @if($loop->parent->parent->first && $loop->parent->first && $loop->first) id="tour-btn-galeria" @endif
                                                onclick="toggleGaleria('galeria-{{ $reporte->id }}', event)" 
                                                class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>Ver Fotos de Evidencia</span>
                                            </button>

                                            {{-- Galería Oculta --}}
                                            <div id="galeria-{{ $reporte->id }}" class="hidden mt-4 pt-4 border-t border-slate-200">
                                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                                    @foreach(explode(',', $reporte->foto_path) as $foto)
                                                        <a href="{{ asset('storage/' . trim($foto)) }}" target="_blank" class="block">
                                                            <img src="{{ asset('storage/' . trim($foto)) }}" 
                                                                class="h-20 w-full object-cover rounded-xl border border-slate-200 shadow-sm active:scale-95 transition-transform">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="text-center py-20">
                    <p class="text-slate-500 font-bold uppercase text-xs">No hay historial para esta unidad.</p>
                </div>
            @endforelse

            {{-- Mensaje para cuando no hay resultados en la búsqueda --}}
            <div id="noResults" class="hidden text-center py-10">
                <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">No se encontraron tareas con ese nombre</p>
            </div>
        </div>
    </div>

    {{-- Scripts Internos (Mantener igual) --}}
    <script>
        document.getElementById('busquedaTarea').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.card-tarea');
            let hasResults = false;

            cards.forEach(card => {
                const nombre = card.getAttribute('data-nombre');
                if (nombre.includes(term)) {
                    card.style.display = 'block';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noResults').classList.toggle('hidden', hasResults);
        });

        function toggleGaleria(id, event) {
            const galeria = document.getElementById(id);
            const boton = event.currentTarget;
            
            if (galeria.classList.contains('hidden')) {
                galeria.classList.remove('hidden');
                boton.classList.add('text-blue-600');
                boton.querySelector('span').innerText = 'Ocultar Fotos';
            } else {
                galeria.classList.add('hidden');
                boton.classList.remove('text-blue-600');
                boton.querySelector('span').innerText = 'Ver Fotos de Evidencia';
            }
        }
    </script>
</x-app-layout>