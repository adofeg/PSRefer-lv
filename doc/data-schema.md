# Modelos de Datos Dinámicos (Schemaless)

Para permitir que cada oferta del catálogo tenga campos diferentes (ej: una hipoteca pide "monto", un seguro pide "marca de auto"), el sistema utiliza un esquema de datos dinámico.

## 📝 Referidos y Metadata

El modelo `Referral` ya no tiene columnas fijas para los datos del cliente. Todo se guarda en la columna `metadata` (tipo JSON):
- `metadata->client_name`: Nombre del cliente referido.
- `metadata->client_email`: Email del cliente.
- `metadata->client_phone`: Teléfono del cliente.
- `metadata->...`: Cualquier otro campo definido en el formulario de la oferta.

## 📋 Validación Basada en Esquema

Cuando se crea un referido:
1. Se carga el `form_schema` (JSON) de la Oferta desde el catálogo.
2. El `FormSchemaValidator` valida que los datos enviados en `form_data` cumplan con las reglas definidas dinámicamente.
3. El `SubmitReferralAction` extrae los campos clave para guardarlos en la estructura JSON del modelo.

## 🔍 Ventajas
- No se requieren migraciones de base de datos para añadir formularios nuevos.
- Flexibilidad total para escalar a cualquier tipo de producto o servicio.
