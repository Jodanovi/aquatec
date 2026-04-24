<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-6">
            
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="bg-slate-900 p-8 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-blue-400 font-black uppercase text-xs tracking-widest mb-1">Revisión de Planificación</h2>
                        <p class="text-3xl font-black italic">OT: {{ $ot->numero_ot }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Creado por: <span class="text-white">{{ $ot->creador->name ?? 'Sistema' }}</span></p>
                        @if($ot->editor)
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Última modificación: <span class="text-white">{{ $ot->editor->name }}</span></p>
                        @endif
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-6">
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <h3 class="text-slate-400 font-black text-[10px] uppercase mb-4 tracking-tighter">Datos Generales</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Cliente / Unidad</span>
                                    <p class="font-bold text-slate-800">{{ $ot->cliente }} - {{ $ot->embarcacion_unidad }}</p>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-400 uppercase font-bold">Lugar de Trabajo</span>
                                    <p class="font-bold text-slate-800">{{ $ot->lugar }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                            <h3 class="text-blue-400 font-black text-[10px] uppercase mb-2 tracking-tighter">Indicaciones Logísticas</h3>
                            <p class="text-sm font-medium text-slate-700 leading-relaxed italic">
                                "{{ $ot->indicaciones->last()->indicacion ?? 'Sin indicaciones' }}"
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-6">
                        <h3 class="font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                            Checklist de Tareas para el Técnico
                            <span class="bg-slate-100 text-slate-500 text-[10px] px-2 py-1 rounded-md">{{ $ot->tareas->count() }} tareas</span>
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($ot->tareas as $tarea)
                                <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-blue-200 transition-all">
                                    <div class="w-6 h-6 border-2 border-blue-200 rounded-lg flex-shrink-0"></div>
                                    <span class="font-bold text-slate-700 uppercase text-sm">{{ $tarea->descripcion_tarea }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-10 flex gap-4">
                            <a href="{{ route('orden-trabajos.edit', $ot->id) }}" 
                               class="flex-1 bg-white border-2 border-slate-200 text-slate-400 py-4 rounded-2xl font-black uppercase text-xs tracking-widest text-center hover:bg-slate-50 transition-all">
                                Corregir Datos
                            </a>

                            <form action="{{ route('orden-trabajos.activar', $ot->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-green-600 transition-all shadow-lg shadow-blue-200">
                                    Confirmar y Activar OT
                                </button>
                            </form>
                        </div>
                        <p class="text-center text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                            * Al activar, el Técnico podrá visualizarla y ya no se podrá modificar el checklist.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>