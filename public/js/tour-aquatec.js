document.addEventListener('DOMContentLoaded', function() {
    const driver = window.driver.js.driver;
    const path = window.location.pathname;

    // localStorage.clear();

    // --- CONFIGURACIÓN BASE ---
    const configBase = {
        nextBtnText: 'Siguiente →',
        prevBtnText: '← Anterior',
        doneBtnText: 'Finalizar',
        showProgress: true,
        progressText: 'Paso {{current}} de {{total}}',
        allowClose: true,
    };

    // --- 1. HISTORIAL DE UNIDAD (PRIORIDAD PARA EVITAR CRUCE CON EJECUCIÓN) ---
    if (path.includes('/historial/')) {
        const stepsHistorial = [];
        if (document.querySelector('#busquedaTarea')) {
            stepsHistorial.push({ 
                element: '#busquedaTarea', 
                popover: { title: 'Buscador de Historial', description: 'Filtra tareas terminadas por nombre.', position: 'bottom' }
            });
        }
        if (document.querySelector('#tour-primera-tarea')) {
            stepsHistorial.push({ 
                element: '#tour-primera-tarea', 
                popover: { title: 'Registros', description: 'Detalles de trabajos finalizados anteriormente.', position: 'top' }
            });
        }
        const historialTour = driver({ ...configBase, steps: stepsHistorial });
        ejecutarTour('visto_historial_v13', historialTour);
    }

    // --- 2. NUEVA ORDEN (CREAR) ---
    else if (path.includes('orden_trabajos/create')) {
        const crearTour = driver({
            ...configBase,
            steps: [
                { element: 'input[name="cliente"]', popover: { title: 'Cliente', description: 'Selecciona o escribe el nombre del cliente.', position: 'bottom' }},
                { element: 'input[name="embarcacion_unidad"]', popover: { title: 'Unidad', description: 'Nombre de la embarcación o equipo.', position: 'bottom' }},
                { element: 'input[name="lugar"]', popover: { title: 'Ubicación', description: 'Lugar del trabajo.', position: 'bottom' }},
                { 
                    element: '#tour-fecha', 
                    popover: { title: 'Fecha Vigencia', description: 'Establece la fecha límite aquí.', position: 'top', align: 'center' }
                },
                { 
                    element: '#tour-negociable', 
                    popover: { title: '¿Negociable?', description: 'Marca si la fecha puede cambiar.', position: 'top', align: 'center' }
                },
                { element: 'button[type="submit"]', popover: { title: 'Finalizar', description: 'Guarda y habilita la orden.', position: 'top' }}
            ]
        });
        ejecutarTour('visto_crear_ot_v13', crearTour);
    }

    // --- 3. DASHBOARD (ADAPTATIVO: OPERADOR / TÉCNICO / JEFE) ---
    else if (path === '/dashboard' || path === '/') {
        const btnNuevaOrden = document.querySelector('a[href*="orden_trabajos/create"]');
        let stepsDashboard = [];

        if (btnNuevaOrden) {
            // Pasos específicos para el Operador
            stepsDashboard.push({ element: 'a[href*="orden_trabajos/create"]', popover: { title: 'Nueva Orden', description: 'Registra una nueva OT desde aquí.', position: 'left' }});
        } else {
            // Pasos para Técnicos/Jefes (Solo si no hay botón de nueva orden)
            if (document.querySelector('.grid')) {
                stepsDashboard.push({ element: '.grid', popover: { title: 'Resumen', description: 'Vista general de los estados de trabajo.', position: 'bottom' }});
            }
        }

        // Paso común para todos
        if (document.querySelector('.overflow-x-auto')) {
            stepsDashboard.push({ element: '.overflow-x-auto', popover: { title: 'Tabla de Órdenes', description: 'Aquí aparece el listado de las OT.', position: 'top' }});
        }

        const dashboardTour = driver({ ...configBase, steps: stepsDashboard });
        ejecutarTour('visto_dashboard_v13', dashboardTour);
    }

    // --- 4. DETALLE DE OT (TÉCNICO / JEFE) ---
    else if (path.includes('/ejecucion/ot/')) {
        let stepsDetalle = [];
        const elementosComunes = [
            { id: '#Historial_Unidad', t: 'Historial', d: 'Consulta trabajos previos de esta unidad.' },
            { id: '#Indicaciones_Logística', t: 'Logística', d: 'Notas de ubicación o prioridad.' },
            { id: '#descripcion_tarea', t: 'Tarea', d: 'Sistema o equipo a intervenir.' },
            { id: '#Procedimiento', t: 'Procedimiento', d: 'Instrucciones técnicas detalladas.' },
            { id: '#reporte_tecnico', t: 'Tu Reporte', d: 'Describe el avance realizado.' },
            { id: '#Fotos', t: 'Evidencias', d: 'Sube fotos de respaldo.' },
            { id: '#enviar_reporte', t: 'Enviar Reporte', d: 'Guarda el avance diario.' }
        ];

        elementosComunes.forEach(el => {
            if (document.querySelector(el.id)) {
                stepsDetalle.push({ element: el.id, popover: { title: el.t, description: el.d, position: 'top' }});
            }
        });

        // Pasos exclusivos para el Jefe de Taller
        if (document.querySelector('#Corrección_Técnica')) {
            stepsDetalle.push({ element: '#Corrección_Técnica', popover: { title: 'Corrección', description: 'Validación profesional del jefe.', position: 'top' }});
        }
        const btnVal = document.querySelector('[id^="form-validar-"]');
        if (btnVal) stepsDetalle.push({ element: btnVal, popover: { title: 'Validar', description: 'Aprobación técnica de la tarea.', position: 'top' }});
        
        if (document.querySelector('#Dictamen_Final')) {
            stepsDetalle.push({ element: '#Dictamen_Final', popover: { title: 'Dictamen', description: 'Cierre definitivo de la orden.', position: 'top' }});
        }
        if (document.querySelector('#Finalizar_Orden')) {
            stepsDetalle.push({ element: '#Finalizar_Orden', popover: { title: 'Cerrar OT', description: 'Finaliza la orden completamente.', position: 'top' }});
        }

        const detalleTour = driver({ ...configBase, steps: stepsDetalle });
        ejecutarTour('visto_detalle_v13', detalleTour);
    }

    // --- 5. LISTADO DE EJECUCIÓN (TÉCNICOS) ---
    else if (path.includes('/ejecucion')) {
        const ejecucionTour = driver({
            ...configBase,
            steps: [{ element: '#tarjeta-ot-ejecucion', popover: { title: 'Órdenes Activas', description: 'Selecciona una orden para trabajar.', position: 'bottom' }}]
        });
        ejecutarTour('visto_ejecucion_v13', ejecucionTour);
    }

    // --- FUNCIÓN DE EJECUCIÓN ---
    function ejecutarTour(key, tourInstance) {
        if (!localStorage.getItem(key)) {
            setTimeout(() => {
                // Solo arranca si no hay otro popover abierto y hay pasos disponibles
                if (document.querySelectorAll('.driver-popover').length === 0 && tourInstance.getConfig().steps.length > 0) {
                    tourInstance.drive();
                    localStorage.setItem(key, 'true');
                }
            }, 700);
        }
    }
    // --- BOTÓN DE AYUDA MANUAL ---
    document.getElementById('reiciarTour')?.addEventListener('click', function() {
        const path = window.location.pathname;

        // Buscamos qué tour corresponde a la ruta actual
        if (path.includes('/historial/')) {
            // Ejecutamos la lógica que ya definiste arriba pero forzada
            location.reload(); // Forma sencilla: recargar y limpiar solo esa llave
            localStorage.removeItem('visto_historial_v13');
        } 
        else if (path.includes('orden_trabajos/create')) {
            localStorage.removeItem('visto_crear_ot_v13');
            location.reload();
        }
        else if (path === '/dashboard' || path === '/') {
            // Limpiamos ambas posibilidades del dashboard
            localStorage.removeItem('visto_dashboard_v13');
            localStorage.removeItem('visto_dashboard_operador_v13');
            location.reload();
        }
        else if (path.includes('/ejecucion/ot/')) {
            localStorage.removeItem('visto_detalle_v13');
            location.reload();
        }
        else if (path.includes('/ejecucion')) {
            localStorage.removeItem('visto_ejecucion_v13');
            location.reload();
        }
    });
});