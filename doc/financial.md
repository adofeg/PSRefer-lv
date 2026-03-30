# Sistema Financiero y Comisiones

Se ha simplificado el motor financiero eliminando capas de "Overrides" y centralizando todo en el Catálogo de Ofertas.

## 💰 Generación de Comisiones

Las comisiones se generan automáticamente cuando un Referido cambia a estado **Exitoso (Cerrado)**. El proceso es:
1. El usuario cierra el referido.
2. Se dispara el evento `GenerateCommission`.
3. El `CommissionService` utiliza el `CommissionRuleEngine` para calcular el monto.

## 📏 Motor de Reglas (Rule Engine)

El sistema ya no usa tablas de "ajustes" separadas. Ahora, cada **Oferta (Offering)** en el catálogo puede tener reglas de comisión:
- **Regla Base**: Porcentaje por defecto para todos los asociados.
- **Reglas por Asociado**: Reglas específicas creadas dentro de la Oferta para ciertos asociados.

## 💳 Gestión de Pagos

- **Estatus de Comisión**: `pending` (Pendiente), `paid` (Pagado), `void` (Anulada).
- **Control de Saldo**: El campo `balance` en el modelo `Associate` se actualiza automáticamente:
  - Sube cuando una comisión se marca como `paid`.
  - Baja si una comisión pagada se marca como `void`.
- **Comprobantes**: Se requiere la subida de un archivo (imagen/PDF) como comprobante para marcar una comisión como pagada desde `Edit.vue`.

## ⚠️ Registro de Cambios
- Se eliminó el modelo `CommissionOverride`.
- Se eliminó la tabla `associate_offering_commissions`.
- Se eliminó el controlador `CommissionOverrideController`.
