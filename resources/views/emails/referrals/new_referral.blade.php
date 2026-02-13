@component('mail::message')
# Nuevo Referido Recibido

Se ha registrado un nuevo referido en la plataforma.

**Servicio:** {{ $referral->offering->name }}

## Información del Cliente
**Nombre:** {{ $referral->client_name }}
**Contacto:** {{ $referral->client_contact }}
**Estado de Residencia:** {{ $referral->metadata['client_state'] ?? 'No especificado' }}

## Referido Por
**Asociado:** {{ $referral->associate->user->name ?? 'N/A' }}
**Email:** {{ $referral->associate->user->email ?? 'N/A' }}

@component('mail::button', ['url' => route('admin.referrals.show', $referral->id)])
Ver Referido
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
