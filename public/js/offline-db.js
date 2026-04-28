// Inicializar la base de datos local
const db = new Dexie("AquatecOffline");

// Definimos la estructura
// ++id: llave primaria autoincremental
// ot_id, tarea_id: para identificar el trabajo
// texto, foto: el contenido del reporte
// sincronizado: 0 si está pendiente de subir, 1 si ya se subió
db.version(1).stores({
    reportes: '++id, ot_id, tarea_id, sincronizado'
});

console.log("Base de datos local Aquatec lista para trabajar offline.");

// Función para guardar reporte si no hay internet
async function guardarReporteLocal(otId, tareaId, texto, fotoBase64) {
    try {
        await db.reportes.add({
            ot_id: otId,
            tarea_id: tareaId,
            texto: texto,
            foto: fotoBase64,
            sincronizado: 0,
            fecha: new Date().toISOString()
        });
        console.log("Reporte guardado en el dispositivo.");
        return true;
    } catch (error) {
        console.error("Error al guardar localmente:", error);
        return false;
    }
}