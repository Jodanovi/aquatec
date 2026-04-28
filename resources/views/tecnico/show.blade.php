<x-app-layout>
    <div class="bg-slate-50 min-h-screen pb-20">
        {{-- Cabecera Fija --}}
        <div class="bg-slate-900 text-white p-4 sticky top-0 z-50 shadow-lg">
            <div class="flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="text-blue-400 p-2 hover:bg-slate-800 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="text-center">
                    <h1 class="text-[10px] font-black uppercase tracking-widest text-blue-400">Orden de Trabajo</h1>
                    <p class="text-lg font-bold italic">{{ $ot->numero_ot }}</p>
                    
                    {{-- BOTÓN DE HISTORIAL DE UNIDAD --}}
                    <a id="Historial_Unidad" href="{{ route('unidades.historial', $ot->id) }}" 
                        class="mt-2 flex items-center justify-center gap-1 text-[10px] font-black text-slate-300 uppercase hover:text-white transition-colors bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700 shadow-sm">
                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Historial: {{ $ot->embarcacion_unidad }}
                    </a>
                </div>
                <div class="w-10"></div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto p-4 space-y-6">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-2xl text-xs font-bold uppercase shadow-lg text-center animate-pulse">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Sección de Logística --}}
            <div id="Indicaciones_Logística" class="bg-blue-600 rounded-3xl p-5 text-white shadow-xl">
                <h3 class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-2 text-blue-100">Indicaciones de Logística</h3>
                <p class="font-medium leading-relaxed italic text-sm">
                    "{{ $ot->indicaciones->last()->indicacion ?? 'Sin indicaciones específicas.' }}"
                </p>
            </div>

            {{-- Listado de Tareas --}}
            <div class="space-y-4">
                <h2 class="font-black uppercase text-slate-700 text-sm ml-2 tracking-tight">Tareas Asignadas</h2>
                
                @foreach($ot->tareas as $tarea)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-4">
                    <div class="p-5">
                        <div id="descripcion_tarea" class="flex items-start justify-between gap-4 mb-4">
                            <h4 class="font-black text-slate-800 uppercase text-sm italic">{{ $tarea->descripcion_tarea }}</h4>
                        </div>

                        {{-- INDICACIONES TÉCNICAS DEL JEFE --}}
                        @if($tarea->indicaciones_tecnicas)
                            <div id="Procedimiento" class="mt-3 mb-5 p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Procedimiento a seguir:</span>
                                </div>
                                <p class="text-slate-700 text-xs font-bold uppercase italic leading-relaxed">
                                    {{ $tarea->indicaciones_tecnicas }}
                                </p>
                            </div>
                        @endif

                        {{-- REPORTES DE TÉCNICOS (HISTORIAL DE LA OT ACTUAL) --}}
                        @if($tarea->reportes->count() > 0)
                        <div class="space-y-4 mb-6 border-l-2 border-slate-100 pl-4">
                            @foreach($tarea->reportes as $reporte)
                                <div class="relative bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                                    
                                    @if($ot->estado == 'activa' && ($reporte->user_id == auth()->id() || in_array(auth()->user()->role, ['admin', 'jefe_tecnico'])))
                                    <div class="absolute top-3 right-3 flex gap-2">
                                        {{-- Botón Editar --}}
                                        <button onclick="abrirModalEditar({{ $reporte->id }}, '{{ addslashes($reporte->comentario) }}')" 
                                            class="p-2 bg-white border border-slate-200 rounded-xl text-blue-600 shadow-sm hover:bg-blue-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        
                                        {{-- Botón Eliminar Reporte --}}
                                        <form action="{{ route('tecnico.reporte.eliminar', $reporte->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este reporte técnico completo?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-white border border-slate-200 rounded-xl text-red-500 shadow-sm hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @endif

                                    <div class="text-[10px] flex flex-col text-slate-400 mb-2 pr-16 space-y-0.5">
                                        <div class="flex justify-between">
                                            <span class="font-bold uppercase text-blue-600">Reporte de: {{ $reporte->tecnico->name ?? 'Técnico' }}</span>
                                            <span>{{ $reporte->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <p class="text-slate-600 text-xs leading-relaxed italic">"{{ $reporte->comentario }}"</p>
                                    
                                    {{-- FOTOS --}}
                                    @if($reporte->foto_path)
                                    <div class="flex flex-wrap gap-3 mt-3">
                                        @foreach(explode(',', $reporte->foto_path) as $foto)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . trim($foto)) }}" class="w-20 h-20 object-cover rounded-lg border border-slate-200 shadow-sm">
                                                
                                                @if($ot->estado == 'activa' && ($reporte->user_id == auth()->id() || in_array(auth()->user()->role, ['admin', 'jefe_tecnico'])))
                                                <form action="{{ route('tecnico.foto.eliminar', $reporte->id) }}" method="POST" class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="foto_path" value="{{ trim($foto) }}">
                                                    <button type="submit" onclick="return confirm('¿Eliminar imagen?')" class="bg-red-600 text-white rounded-full p-1 shadow-lg hover:scale-110">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- VALIDACIÓN PROFESIONAL (Admin/Jefe) --}}
                        @if(auth()->user()->role !== 'tecnico')
                            <div id="Corrección_Técnica" class="mb-6 p-4 rounded-2xl border-2 {{ $tarea->validacion_profesional ? 'border-blue-500 bg-blue-50' : 'border-dashed border-slate-200 bg-slate-50' }}">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="{{ $tarea->validacion_profesional ? 'bg-blue-600' : 'bg-slate-400' }} p-1.5 rounded-lg text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <h5 class="text-[10px] font-black uppercase tracking-wider {{ $tarea->validacion_profesional ? 'text-blue-700' : 'text-slate-500' }}">
                                            Corrección Técnica Profesional
                                        </h5>
                                    </div>
                                    @if($tarea->validacion_profesional && $ot->estado != 'finalizada' && in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                                        <button onclick="document.getElementById('form-validar-{{ $tarea->id }}').classList.toggle('hidden');" 
                                            class="text-[9px] bg-white border border-blue-200 text-blue-600 px-2 py-1 rounded-md font-bold uppercase">
                                            Corregir
                                        </button>
                                    @endif
                                </div>

                                @if($tarea->validacion_profesional)
                                    <p class="text-xs text-slate-700 font-bold italic mb-2 leading-relaxed">"{{ $tarea->validacion_profesional }}"</p>
                                @endif

                                @if($ot->estado != 'finalizada' && in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
                                    <div id="form-validar-{{ $tarea->id }}" class="{{ $tarea->validacion_profesional ? 'hidden' : '' }} mt-3">
                                        <form action="{{ route('tecnico.tarea.validar', $tarea->id) }}" method="POST" class="space-y-3">
                                            @csrf
                                            <textarea name="validacion_profesional" required class="w-full text-xs border-slate-200 rounded-xl focus:ring-blue-500 p-3 bg-white" placeholder="Escriba la validación técnica profesional...">{{ $tarea->validacion_profesional }}</textarea>
                                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg text-[10px] font-black uppercase shadow-md">Guardar Validación</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- FORMULARIO NUEVO REPORTE --}}
                        @if($ot->estado == 'activa')
                            <form action="{{ route('tecnico.reporte.guardar', $tarea->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                                @csrf
                                <textarea id="reporte_tecnico" name="comentario" required rows="2" class="w-full border-slate-200 rounded-2xl text-xs p-3 bg-slate-50 focus:ring-blue-500" placeholder="Escribe un avance..."></textarea>
                                <div class="flex gap-2">
                                    <label id="Fotos" class="btn-foto-nueva-{{ $tarea->id }} flex-1 bg-white border-2 border-dashed border-slate-200 rounded-2xl flex items-center justify-center gap-2 py-3 cursor-pointer transition-all hover:bg-slate-50">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span class="txt-foto-nueva-{{ $tarea->id }} text-[10px] font-black uppercase text-slate-400">Fotos</span>
                                        <input type="file" name="fotos[]" multiple accept="image/*" class="hidden" onchange="actualizarLabelFoto(this, '{{ $tarea->id }}')">
                                    </label>
                                     <button id="enviar_reporte" type="button" onclick="manejarEnvio(this)" class="bg-blue-600 text-white px-6 rounded-2xl text-[10px] font-black uppercase shadow-lg shadow-blue-200">Enviar</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- CIERRE (Solo Jefes) --}}
            @if($ot->estado == 'activa' && in_array(auth()->user()->role, ['admin', 'jefe_tecnico']))
            <div class="mt-12 bg-slate-900 rounded-3xl p-8 border border-blue-500/30 shadow-2xl">
                <form action="{{ route('orden_trabajos.finalizar', $ot->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <div id="Dictamen_Final" class="mb-6">
                        <label class="block text-blue-400 text-[10px] font-black uppercase mb-2">Dictamen Final</label>
                        <textarea name="conclusion_jefe" rows="4" required class="w-full bg-slate-800 border-slate-700 rounded-2xl text-white text-xs" placeholder="Escriba la validación final..."></textarea>
                    </div>
                    <button id="Finalizar_Orden" type="submit" onclick="return confirm('¿Bloquear esta OT definitivamente?')" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg shadow-blue-900/50">
                        Finalizar y Cerrar Orden
                    </button>
                </form>
            </div>
            @endif

            @if($ot->estado == 'finalizada')
            <div class="mt-8 bg-emerald-50 border-2 border-emerald-200 rounded-3xl p-6">
                <div class="flex items-center gap-3 mb-4 text-emerald-700">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>
                    <h3 class="font-black uppercase text-sm">OT Cerrada</h3>
                </div>
                <p class="text-slate-700 italic text-sm">"{{ $ot->conclusion_jefe }}"</p>
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div id="modalEditar" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-sm font-black uppercase text-slate-800 mb-4 text-center">Editar Avance</h3>
                <form id="formEditar" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf @method('PATCH')
                    <textarea id="editComentario" name="comentario" required class="w-full border-slate-200 rounded-2xl text-sm p-3 bg-slate-50 min-h-[120px] focus:ring-blue-500"></textarea>
                    <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-1">+ Fotos (opcional):</label>
                        <input type="file" name="fotos[]" multiple accept="image/*" class="text-xs w-full">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="cerrarModal()" class="flex-1 py-3 text-xs font-bold uppercase text-slate-400">Cancelar</button>
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl text-xs font-bold uppercase">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalEditar(id, comentario) {
            const modal = document.getElementById('modalEditar');
            const form = document.getElementById('formEditar');
            const textarea = document.getElementById('editComentario');
            form.action = `/ejecucion/reporte/${id}/actualizar`;
            textarea.value = comentario;
            modal.classList.remove('hidden');
        }
        function cerrarModal() {
            document.getElementById('modalEditar').classList.add('hidden');
        }
        function actualizarLabelFoto(input, tareaId) {
            let label = document.querySelector('.btn-foto-nueva-' + tareaId);
            let text = document.querySelector('.txt-foto-nueva-' + tareaId);
            if (input.files.length > 0) {
                label.classList.add('bg-green-100', 'text-green-700', 'border-green-300');
                text.innerText = input.files.length + ' Foto(s) lista(s)';
            }
        }
    </script>
    {{-- LÓGICA DE ENVÍO SILENCIOSO, OFFLINE Y AUTO-SYNC (VERSIÓN SIN BUCLE) --}}
    <script src="https://unpkg.com/dexie/dist/dexie.js"></script>
    <script>
        const db = new Dexie("AquatecOffline");
        db.version(1).stores({
            reportes: '++id, tarea_id, comentario, sincronizado'
        });

        // Variable para evitar bucles de sincronización
        let estaSincronizando = false;

        async function manejarEnvio(btn) {
            const form = btn.closest('form');
            const formData = new FormData(form);
            const tareaId = form.action.split('/').pop();
            const comentario = formData.get('comentario');

            if (!comentario.trim()) {
                alert("Escribe un avance primero.");
                return;
            }

            if (!navigator.onLine) {
                await db.reportes.add({
                    tarea_id: tareaId,
                    comentario: comentario,
                    fecha: new Date().toISOString(),
                    sincronizado: 0
                });

                btn.classList.remove('bg-blue-600');
                btn.classList.add('bg-orange-500');
                btn.innerText = 'Guardado Local';
                form.reset();
                alert('⚠️ Estás offline. Guardado en el celular.');
                return;
            }

            btn.innerText = 'Enviando...';
            btn.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    btn.innerText = '¡Enviado!';
                    btn.classList.replace('bg-blue-600', 'bg-green-600');
                    form.reset();
                    setTimeout(() => { location.reload(); }, 1500);
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerText = 'Error';
            }
        }

        async function sincronizarReportesPendientes() {
            // Si ya se está sincronizando o estamos offline, no hacer nada
            if (estaSincronizando || !navigator.onLine) return;

            const pendientes = await db.reportes.where('sincronizado').equals(0).toArray();
            if (pendientes.length === 0) return;

            estaSincronizando = true; // Bloqueamos nuevas ejecuciones
            console.log(`Sincronizando ${pendientes.length} reportes...`);

            let exitos = 0;
            for (const reporte of pendientes) {
                const formData = new FormData();
                formData.append('comentario', reporte.comentario);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                try {
                    const response = await fetch(`/ejecucion/reporte/${reporte.tarea_id}/guardar`, {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        await db.reportes.delete(reporte.id);
                        exitos++;
                    }
                } catch (err) {
                    console.error("Error en uno de los reportes:", err);
                }
            }

            if (exitos > 0) {
                alert(`✅ Se han sincronizado ${exitos} reporte(s) pendiente(s).`);
                location.reload(); 
            }
            
            estaSincronizando = false;
        }

        // Detectar cambios de conexión
        window.addEventListener('online', sincronizarReportesPendientes);

        // Ejecutar al cargar la página pero con un pequeño retraso
        window.addEventListener('load', () => {
            setTimeout(sincronizarReportesPendientes, 2000);
        });
    </script>
    
</x-app-layout>