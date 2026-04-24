<x-app-layout>
    <div class="py-12 bg-[#f1f5f9] min-h-screen">
        <div class="max-w-4xl mx-auto px-6">
            {{-- Encabezado --}}
            <div class="bg-slate-900 rounded-t-3xl p-6 border-b border-slate-800 shadow-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-blue-400 font-black uppercase text-xs tracking-widest">Planificación Técnica</h2>
                        <p class="text-white text-2xl font-black italic">OT: {{ $ot->numero_ot }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-400 text-[10px] font-bold uppercase block">Cliente</span>
                        <span class="text-white font-bold">{{ $ot->cliente }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-b-3xl shadow-2xl overflow-hidden border border-gray-200 p-8 space-y-8">
                
                {{-- SECCIÓN DE ACTIVACIÓN --}}
                @if($ot->estado == 'en_planificacion')
                <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6 mb-4 animate-pulse">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="bg-green-600 text-white p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-green-800 font-black uppercase text-sm italic">Planificación Lista</h3>
                            <p class="text-green-600 text-xs font-bold">Revise los datos abajo y si todo está correcto, active la orden para que el técnico pueda trabajar.</p>
                        </div>
                        <form action="{{ route('orden_trabajos.activar', $ot->id) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 rounded-xl shadow-lg shadow-green-200 transition-all uppercase tracking-widest text-xs">
                                🚀 Activar Orden de Trabajo Ahora
                            </button>
                        </form>
                    </div>
                </div>
                <hr class="border-gray-100">
                @endif

                <form action="{{ route('orden_trabajos.update', $ot->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- 1. Logística General --}}
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                            <h3 class="font-black uppercase text-gray-700 tracking-tighter">Indicaciones de Logística (General)</h3>
                        </div>
                        <textarea name="indicacion" required 
                            class="w-full border-gray-200 rounded-2xl font-medium p-4 focus:ring-blue-500 focus:border-blue-500 min-h-[100px]" 
                            placeholder="Ej: El equipo de buceo debe estar listo en muelle a las 08:00 hs...">{{ old('indicacion', $ot->indicaciones->last()->indicacion ?? '') }}</textarea>
                    </div>

                    <hr class="border-gray-100">

                    {{-- 2. Tareas Detalladas --}}
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                                <h3 class="font-black uppercase text-gray-700 tracking-tighter">Tareas e Instrucciones Técnicas</h3>
                            </div>
                            <button type="button" onclick="agregarTarea()" 
                                class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white transition-all">
                                + Agregar Tarea
                            </button>
                        </div>

                        <div id="contenedor-tareas" class="space-y-4">
                            @if($ot->tareas->count() > 0)
                                @foreach($ot->tareas as $index => $tarea)
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3 relative group">
                                        <div class="flex gap-3 items-center">
                                            <span class="text-[10px] font-black text-slate-400">#{{ $index + 1 }}</span>
                                            <input type="text" name="tareas[{{ $index }}][descripcion]" value="{{ $tarea->descripcion_tarea }}" required 
                                                class="flex-1 border-gray-200 rounded-xl font-bold uppercase p-3 text-sm focus:ring-blue-500" placeholder="Descripción de la tarea...">
                                            
                                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 px-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <textarea name="tareas[{{ $index }}][indicaciones]" 
                                            class="w-full border-gray-200 rounded-xl text-xs font-medium p-3 focus:ring-blue-400 focus:border-blue-400 bg-white" 
                                            placeholder="Indicaciones técnicas específicas para esta tarea (opcional)...">{{ $tarea->indicaciones_tecnicas }}</textarea>
                                    </div>
                                @endforeach
                            @else
                                {{-- Estado inicial si no hay tareas --}}
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3">
                                    <div class="flex gap-3 items-center">
                                        <span class="text-[10px] font-black text-slate-400">#1</span>
                                        <input type="text" name="tareas[0][descripcion]" required 
                                            class="flex-1 border-gray-200 rounded-xl font-bold uppercase p-3 text-sm focus:ring-blue-500" 
                                            placeholder="Tarea 1: Descripción...">
                                    </div>
                                    <textarea name="tareas[0][indicaciones]" 
                                        class="w-full border-gray-200 rounded-xl text-xs font-medium p-3 focus:ring-blue-400 focus:border-blue-400 bg-white" 
                                        placeholder="Indicaciones técnicas específicas para esta tarea..."></textarea>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-8 flex justify-between items-center border-t border-gray-50">
                        <button type="submit" class="order-2 bg-slate-800 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-blue-600 transition-all shadow-lg">
                            {{ $ot->estado == 'en_planificacion' ? 'Actualizar Planificación' : 'Guardar y Revisar' }}
                        </button>
                </form>

                <form action="{{ route('orden_trabajos.cancelar', $ot->id) }}" method="POST" class="order-1">
                    @csrf
                    <button type="submit" onclick="return confirm('¿Estás seguro de cancelar? Se borrarán las tareas e indicaciones cargadas.')" 
                        class="text-gray-400 font-black uppercase text-[10px] hover:text-red-500 transition-colors">
                        Cancelar y Limpiar
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicializamos el contador con la cantidad de tareas existentes
        let contador = {{ $ot->tareas->count() > 0 ? $ot->tareas->count() : 1 }};
        
        function agregarTarea() {
            const contenedor = document.getElementById('contenedor-tareas');
            const nuevaTarea = document.createElement('div');
            nuevaTarea.className = 'bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3 animate-fade-in-down';
            
            // Usamos el valor actual de contador para el índice del array
            const indice = contador;
            
            nuevaTarea.innerHTML = `
                <div class="flex gap-3 items-center">
                    <span class="text-[10px] font-black text-slate-400">#${indice + 1}</span>
                    <input type="text" name="tareas[${indice}][descripcion]" required 
                        class="flex-1 border-gray-200 rounded-xl font-bold uppercase p-3 text-sm focus:ring-blue-500" 
                        placeholder="Tarea ${indice + 1}: Descripción...">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 px-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                <textarea name="tareas[${indice}][indicaciones]" 
                    class="w-full border-gray-200 rounded-xl text-xs font-medium p-3 focus:ring-blue-400 focus:border-blue-400 bg-white" 
                    placeholder="Indicaciones técnicas específicas para esta tarea..."></textarea>
            `;
            
            contenedor.appendChild(nuevaTarea);
            contador++; // Incrementamos para que la siguiente tarea tenga un índice único
        }
    </script>

    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
    </style>
</x-app-layout>