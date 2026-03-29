# PS Refer - Documentación de Arquitectura y Cambios

Este directorio contiene la documentación técnica de las refactorizaciones y mejoras implementadas en el sistema PS Refer (Laravel + Vue 3).

## 📂 Estructura de Documentación

- [**Arquitectura de Capas (Layers)**](architecture.md): Detalle de la separación entre Administradores y Asociados.
- [**Sistema Financiero y Comisiones**](financial.md): Lógica de cálculos, reglas de catálogo y gestión de pagos.
- [**Configuración y Notificaciones (SMTP)**](notifications.md): Guía de configuración dinámica de correo y colas.
- [**Modelos de Datos Dinámicos**](data-schema.md): Uso de metadata y esquemas JSON en Referidos.

## 🚀 Resumen de la Última Sesión (Marzo 2026)

En esta sesión se transformó el sistema de una estructura monolítica a una **Arquitectura de Capas** profesional, eliminando redundancias y mejorando la seguridad.

### Hitos Clave:
1. **Consolidación Financiera**: Eliminación de "Overrides" y centralización en el Motor de Reglas.
2. **Layered Security**: Separación total de lógica de negocio entre `Employee` (Admin) y `Associate`.
3. **Dynamic SMTP**: Sistema autoconfigurable desde el panel de administración.
4. **Schemaless Referrals**: Los datos del cliente ahora son 100% dinámicos basados en el catálogo.

---
*PS Refer System - 2026*
