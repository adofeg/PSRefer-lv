<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormSchemaValidator
{
    /**
     * Validate data against a form schema.
     *
     * @param array $schema The form schema definition
     * @param array $data The data to validate
     * @return array The validated data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $schema, array $data): array
    {
        $rules = $this->buildValidationRules($schema);
        $messages = $this->buildValidationMessages($schema);
        
        return Validator::make($data, $rules, $messages)->validate();
    }

    /**
     * Build Laravel validation rules from schema.
     *
     * @param array $schema
     * @return array
     */
    private function buildValidationRules(array $schema): array
    {
        $rules = [];
        
        foreach ($schema as $field) {
            $fieldName = $field['name'] ?? null;
            if (!$fieldName) {
                continue;
            }
            
            $fieldRules = [];
            
            // Required
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            // Type-based validation
            switch ($field['type'] ?? 'text') {
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field['min'])) {
                        $fieldRules[] = "min:{$field['min']}";
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = "max:{$field['max']}";
                    }
                    break;
                    
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                    
                case 'tel':
                case 'phone':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'regex:/^[0-9\s\-\+\(\)]+$/';
                    break;
                    
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                    
                case 'select':
                    if (!empty($field['options'])) {
                        $options = is_array($field['options']) 
                            ? $field['options'] 
                            : explode(',', $field['options']);
                        $fieldRules[] = 'in:' . implode(',', $options);
                    }
                    break;
                    
                case 'text':
                case 'textarea':
                default:
                    $fieldRules[] = 'string';
                    if (isset($field['maxlength'])) {
                        $fieldRules[] = "max:{$field['maxlength']}";
                    }
                    break;
            }
            
            $rules[$fieldName] = implode('|', $fieldRules);
        }
        
        return $rules;
    }

    /**
     * Build custom validation messages.
     *
     * @param array $schema
     * @return array
     */
    private function buildValidationMessages(array $schema): array
    {
        $messages = [];
        
        foreach ($schema as $field) {
            $fieldName = $field['name'] ?? null;
            $fieldLabel = $field['label'] ?? $fieldName;
            
            if (!$fieldName) {
                continue;
            }
            
            $messages["{$fieldName}.required"] = "El campo {$fieldLabel} es obligatorio.";
            $messages["{$fieldName}.email"] = "El campo {$fieldLabel} debe ser un email válido.";
            $messages["{$fieldName}.numeric"] = "El campo {$fieldLabel} debe ser un número.";
            $messages["{$fieldName}.url"] = "El campo {$fieldLabel} debe ser una URL válida.";
        }
        
        return $messages;
    }

    /**
     * Get default value for a field.
     *
     * @param array $field
     * @return mixed
     */
    public function getDefaultValue(array $field)
    {
        return $field['default'] ?? ($field['type'] === 'number' ? 0 : '');
    }

    /**
     * Validate the schema structure itself.
     *
     * @param mixed $schema
     * @return bool
     */
    public function isValidSchema($schema): bool
    {
        if (!is_array($schema)) {
            return false;
        }
        
        foreach ($schema as $field) {
            if (!is_array($field)) {
                return false;
            }
            
            // Must have name and type
            if (!isset($field['name']) || !isset($field['type'])) {
                return false;
            }
            
            // Validate field name format (alphanumeric + underscore)
            if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $field['name'])) {
                return false;
            }
        }
        
        return true;
    }
}
