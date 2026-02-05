<x-mail::message>
# Nuevo Referido Recibido

Se ha registrado un nuevo referido en el sistema.

**Detalles del Cliente:**
- **Nombre:** {{ $clientName }}
- **Servicio Interesado:** {{ $offeringName }}

**Referido por:**
- **Asociado:** {{ $referrerName }}

@if($notes)
**Notas:**
{{ $notes }}
@endif

<x-mail::button :url="$url">
Ver Detalles en el Panel
</x-mail::button>

Gracias,<br>
Gerencia de PS Refer
</x-mail::message>
