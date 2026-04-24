<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6">
            <div class="mb-8">
                <h2 class="text-slate-900 text-3xl font-black italic">Orden de Trabajo</h2>
            </div>

            <div class="grid gap-4">
                @forelse($ots as $ot)
                    <a href="{{ route('tecnico.show', $ot->id) }}" class="group">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm group-hover:border-blue-500 group-hover:shadow-md transition-all flex justify-between items-center">
                            <div>
                                <span class="text-blue-600 font-black text-xs uppercase tracking-tighter">Orden de Trabajo</span>
                                <h3 class="text-xl font-bold text-slate-800">{{ $ot->numero_ot }}</h3>
                                <p class="text-slate-500 text-sm font-medium">{{ $ot->cliente }}</p>
                            </div>
                            <div class="text-right">
                                <span class="bg-green-100 text-green-700 text-[10px] font-black px-3 py-1 rounded-full uppercase">Activa</span>
                                <div class="mt-2 text-slate-400 group-hover:text-blue-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bg-slate-200 border-2 border-dashed border-slate-300 rounded-3xl p-12 text-center">
                        <p class="text-slate-500 font-bold italic">No hay órdenes de trabajo activas en este momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>