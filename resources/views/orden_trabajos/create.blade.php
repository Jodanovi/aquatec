<x-app-layout>
    <div class="py-12 bg-[#f1f5f9] min-h-screen">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200">
                <div class="bg-blue-600 p-4 text-center">
                    <h2 class="text-white font-black uppercase tracking-tighter">Apertura de Orden de Trabajo</h2>
                </div>

                <form action="{{ route('orden_trabajos.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-2xl border-2 border-dashed border-gray-200">
                            <label class="block text-[10px] font-black text-gray-400 uppercase">1. Número de OT (Automático)</label>
                            <input type="text" value="{{ $proximaOt }}" readonly class="w-full bg-transparent border-none text-2xl font-black text-blue-600 p-0 focus:ring-0">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">2. Cliente</label>
                            <input list="clientesList" name="cliente" required class="w-full border-gray-200 rounded-xl font-bold uppercase p-3" placeholder="Escriba o seleccione...">
                            <datalist id="clientesList">
                                @foreach($clientesExistentes as $c) <option value="{{ $c }}"> @endforeach
                            </datalist>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">3. Embarcación / Unidad</label>
                            <input type="text" name="embarcacion_unidad" required class="w-full border-gray-200 rounded-xl font-bold uppercase p-3" placeholder="Ingreso manual...">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">4. Lugar</label>
                            <input type="text" name="lugar" required class="w-full border-gray-200 rounded-xl font-bold uppercase p-3" placeholder="Ej: VARADERO, TALLER...">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">5. Permiso Fecha Vigencia</label>
                            <div class="flex items-center gap-4 bg-gray-50 p-2 rounded-xl border border-gray-200">
                                <div id="tour-fecha" class="flex-1">
                                    <input type="date" name="permiso_fecha_vigencia" class="w-full border-none bg-transparent font-bold focus:ring-0">
                                </div>
                                
                                <label id="tour-negociable" class="flex items-center gap-2 cursor-pointer border-l pl-4 border-gray-300">
                                    <input type="checkbox" name="permiso_negociable" class="rounded text-blue-600 focus:ring-blue-500">
                                    <span class="text-[9px] font-black uppercase text-gray-500">¿Negociable?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="bg-slate-900 text-white px-12 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-blue-600 transition-all shadow-xl">
                            Guardar y Habilitar Jefe Técnico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>