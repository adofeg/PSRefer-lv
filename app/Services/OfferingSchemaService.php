<?php

namespace App\Services;

class OfferingSchemaService
{
    /**
     * Get the default form schema for offerings that don't have one.
     */
    /**
     * Get the default form schema for offerings that don't have one.
     * Returns v2.0 Grouped Schema.
     */
    public function getDefaultSchema(): array
    {
        return [
            'version' => 1,
            'groups' => [
                [
                    'id' => 'group_01',
                    'title' => 'Datos del Cliente',
                    'fields' => [
                        [
                            'name' => 'field_01_01',
                            'label' => 'Nombre y Apellido',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Nombre completo del cliente',
                        ],
                        [
                            'name' => 'field_01_02',
                            'label' => 'Teléfono',
                            'type' => 'tel',
                            'required' => true,
                            'placeholder' => '+1 (555) 000-0000',
                        ],
                        [
                            'name' => 'field_01_03',
                            'label' => 'Correo Electrónico',
                            'type' => 'email',
                            'required' => true,
                            'placeholder' => 'ejemplo@correo.com',
                        ],
                    ],
                ],
                [
                    'id' => 'group_02',
                    'title' => 'Ubicación',
                    'fields' => [
                        [
                            'name' => 'field_02_01',
                            'label' => 'Estado / Provincia',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Ej. Florida, Madrid, etc.',
                        ],
                    ],
                ],
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
     * Check if schema is v2.0 grouped format.
     */
    public function isGroupedSchema(array $schema): bool
    {
        return isset($schema['groups']) && is_array($schema['groups']);
    }

    /**
     * Helper to flatten fields from any schema version.
     */
    public function flattenFields(array $schema): array
    {
        if ($this->isGroupedSchema($schema)) {
            $fields = [];
            foreach ($schema['groups'] as $group) {
                if (!empty($group['fields'])) {
                    $fields = array_merge($fields, $group['fields']);
                }
            }
            return $fields;
        }

        // Legacy flat schema
        return $schema;
    }

    /**
     * Extract core fields from form data based on schema labels since IDs are dynamic.
     */
    public function extractCoreFields(array $schema, array $formData): array
    {
        $flatSchema = $this->flattenFields($schema);
        
        $keywords = [
            'client_name' => ['nombre', 'name', 'apellido', 'fullname', 'cliente'],
            'client_email' => ['email', 'correo', 'mail', 'electrónico'],
            'client_phone' => ['telf', 'tel', 'phone', 'celular', 'móvil'],
            'client_state' => ['estado', 'state', 'provincia', 'location', 'ubicación', 'ciudad'],
        ];

        $extracted = [
            'client_name' => null,
            'client_email' => null,
            'client_phone' => null,
            'client_state' => null,
        ];

        foreach ($flatSchema as $field) {
            $fieldId = $field['name'] ?? null;
            $fieldLabel = strtolower($field['label'] ?? '');
            
            if (!$fieldId || !isset($formData[$fieldId])) continue;

            $value = $formData[$fieldId];

            foreach ($keywords as $coreKey => $terms) {
                if ($extracted[$coreKey] !== null) continue;

                foreach ($terms as $term) {
                    if (str_contains($fieldLabel, $term)) {
                        $extracted[$coreKey] = $value;
                        break 2; // Move to next schema field
                    }
                }
            }
        }

        return array_filter($extracted);
    }
}
