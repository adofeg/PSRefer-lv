# Plan de Pruebas Manuales - Modernización PS Refer

Este documento detalla los pasos para verificar las nuevas funcionalidades implementadas.

## 1. Categorías de Referidos (Sectores de Servicio)
- [x] **Creación (Admin):** Ir a *Referencias > Nuevo*. Verificar que aparezca el dropdown "Sector de Servicio" con opciones como "Realtor", "Contador", "Agente de seguros".
- [x] **Creación (Asociado):** Iniciar sesión como asociado. Ir a *Nueva Referencia*. Verificar que el campo "Sector de Servicio" sea obligatorio y funcione correctamente.
- [x] **Filtrado en Pipeline:** Ir al Pipeline de Admin. Verificar los nuevos filtros por "Servicio" y "Sector". Filtrar por un sector específico y confirmar que solo aparezcan esos referidos.
- [x] **Visualización:** Confirmar que los datos de contacto (Nombre, Email, Teléfono) del cliente se guardan correctamente en los campos del modelo, no solo en metadata.

## 2. Agenda y "Contactar más tarde"
- [x] **Programación:** Cambiar el estado de un referido a "Contactar más tarde". Verificar que pida una fecha de recordatorio.
- [x] **UI:** Verificar que en el modal de cambio de estado ahora diga "Comentarios" en lugar de "Comentario del cambio".
- [x] **Automatización:** (Prueba técnica) Ejecutar `php artisan app:process-referral-reminders`. Confirmar que los referidos cuya fecha de recordatorio sea hoy o anterior pasen automáticamente a estado "Prospecto".

## 3. Limpieza de Comisiones
- [x] **Tipos de Comisión:** Ir a la edición de una Oferta en Admin. Verificar que el dropdown de tipos de comisión incluya: "Fijo ($)", "Porcentaje (%)", "Manual (Negociado)" y "Variable (Según servicio)".
- [ ] **Estados:** Verificar en el listado de comisiones que ya no aparezca la opción de estado "Anulada" (Void).
- [ ] **Dashboard:** Confirmar que el widget "Resumen del Sistema" muestre correctamente el conteo de todos los "Pendientes" (incluyendo Contactados, En Proceso y Contactar Luego).

## 4. Datos del Asociado
- [ ] **Perfil:** Verificar que el asociado pueda guardar su teléfono de pago (Yape/Móvil) en su perfil.
- [ ] **Visualización Admin:** El administrador debe poder ver estos datos de contacto al gestionar comisiones.

## 5. Integridad de la Base de Datos (Migraciones)
- [ ] **Estructura:** El historial de migraciones debe ser limpio. Solo deben existir archivos `create_..._table`. No debe haber archivos `add_..._to_..._table` para los campos recientes.
- [ ] **Reset Completo:** (Opcional) Si el entorno de desarrollo lo permite, ejecutar `php artisan migrate:fresh --seed` para confirmar que el orden de las tablas (Sectores antes de Referidos) es correcto.

---
*Nota: Si encuentras algún error de reactividad (datos que no se actualizan sin refrescar), por favor repórtalo indicando en qué vista ocurrió.*
*Nota v2: No se eliminaron los datos existentes, pero las nuevas migraciones están consolidadas.*
