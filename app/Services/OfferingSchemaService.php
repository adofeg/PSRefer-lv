<?php

namespace App\Services;

class OfferingSchemaService
{
    /**
     * Get the default form schema for offerings that don't have one.
     */
    public function getDefaultSchema(): array
    {
        return [
            [
                'name' => 'client_name',
                'label' => 'Nombre y Apellido',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Nombre completo del cliente',
            ],
            [
                'name' => 'client_email',
                'label' => 'Correo Electrónico',
                'type' => 'email',
                'required' => true,
                'placeholder' => 'ejemplo@correo.com',
            ],
            [
                'name' => 'client_phone',
                'label' => 'Teléfono',
                'type' => 'tel',
                'required' => true,
                'placeholder' => '+1 (555) 000-0000',
            ],
            [
                'name' => 'client_state',
                'label' => 'Estado / Provincia',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Ej. Florida, Madrid, etc.',
            ],
        ];
    }

    /**
     * Merge or provide schema for an offering.
     */
    public function getSchemaForOffering(?array $schema): array
    {
        if (empty($schema)) {
            return $this->getDefaultSchema();
        }

        return $schema;
    }

    /**
     * Extract core fields from form data based on schema and keywords.
     */
    public function extractCoreFields(array $schema, array $formData): array
    {
        $overlaps = [
            'client_name' => ['name', 'apellido', 'fullname', 'cliente'],
            'client_email' => ['email', 'correo', 'mail'],
            'client_phone' => ['phone', 'tel', 'telefono', 'celular', 'mob'],
            'client_state' => ['state', 'estado', 'provincia', 'location', 'residencia'],
        ];

        $extracted = [
            'client_name' => null,
            'client_email' => null,
            'client_phone' => null,
            'client_state' => null,
            'client_contact' => null,
        ];

        foreach ($schema as $field) {
            $fieldName = $field['name'] ?? null;
            if (!$fieldName || !isset($formData[$fieldName])) continue;

            $value = $formData[$fieldName];

            foreach ($overlaps as $coreKey => $keywords) {
                if ($extracted[$coreKey] !== null) continue;

                foreach ($keywords as $kw) {
                    if (str_contains(strtolower($fieldName), $kw)) {
                        $extracted[$coreKey] = $value;
                        break 2;
                    }
                }
            }
        }

        // Fallback for client_name: use the first text field if not found
        if ($extracted['client_name'] === null) {
            foreach ($schema as $field) {
                if (($field['type'] ?? 'text') === 'text') {
                    $extracted['client_name'] = $formData[$field['name']] ?? null;
                    if ($extracted['client_name']) break;
                }
            }
        }

        // Consolidate client_contact
        $email = $extracted['client_email'];
        $phone = $extracted['client_phone'];
        if ($email && $phone) {
            $extracted['client_contact'] = "$email / $phone";
        } else {
            $extracted['client_contact'] = $email ?: $phone;
        }

        return array_filter($extracted);
    }
}
