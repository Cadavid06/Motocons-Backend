<?php

namespace App\Support;

final class Validator
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, array<int, string>>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, mixed>  $files
     * @return array<string, mixed>
     */
    public static function validate(array $data, array $rules, array $messages = [], array $files = []): array
    {
        $errors = [];
        $validated = [];

        foreach ($rules as $field => $fieldRules) {
            $required = in_array('required', $fieldRules, true);
            $nullable = in_array('nullable', $fieldRules, true);
            $isFile = in_array('file', $fieldRules, true);

            $value = $isFile
                ? ($files[$field] ?? null)
                : self::value($data, $field);

            $missing = $isFile
                ? ! is_array($value) || (int) ($value['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
                : $value === null || $value === '';

            if ($required && $missing) {
                self::addError($errors, $messages, $field, 'required');
                continue;
            }

            if ($missing || $value === null || $value === '') {
                if ($nullable && array_key_exists($field, $data)) {
                    $validated[$field] = null;
                }

                continue;
            }

            $fieldHasError = false;

            foreach ($fieldRules as $rule) {
                [$name, $parameter] = self::parseRule($rule);

                switch ($name) {
                    case 'required':
                    case 'nullable':
                        break;

                    case 'string':
                        if (! is_string($value)) {
                            self::addError($errors, $messages, $field, 'string');
                            $fieldHasError = true;
                        }
                        break;

                    case 'max':
                        $limit = (int) $parameter;

                        if (is_array($value)) {
                            if ((int) ($value['size'] ?? 0) > $limit * 1024) {
                                self::addError($errors, $messages, $field, 'max', ['n' => (string) $limit]);
                                $fieldHasError = true;
                            }
                        } elseif (is_string($value) && mb_strlen($value) > $limit) {
                            self::addError($errors, $messages, $field, 'max', ['n' => (string) $limit]);
                            $fieldHasError = true;
                        }
                        break;

                    case 'in':
                        $options = explode(',', (string) $parameter);

                        if (! in_array((string) $value, $options, true)) {
                            self::addError($errors, $messages, $field, 'in');
                            $fieldHasError = true;
                        }
                        break;

                    case 'date':
                        if (! is_string($value) || strtotime($value) === false) {
                            self::addError($errors, $messages, $field, 'date');
                            $fieldHasError = true;
                        }
                        break;

                    case 'file':
                        if (! is_array($value) || (int) ($value['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                            self::addError($errors, $messages, $field, 'file');
                            $fieldHasError = true;
                        }
                        break;

                    case 'mimes':
                        $allowed = array_map('strtolower', explode(',', (string) $parameter));
                        $tmpName = is_array($value) ? (string) ($value['tmp_name'] ?? '') : '';
                        $mime = $tmpName !== '' ? Storage::detectMime($tmpName) : null;
                        $extension = Storage::extensionForMime($mime);

                        if ($extension === null || ! in_array($extension, $allowed, true)) {
                            self::addError($errors, $messages, $field, 'mimes', [
                                'list' => implode(', ', $allowed),
                            ]);
                            $fieldHasError = true;
                        }
                        break;
                }
            }

            if (! $fieldHasError) {
                $validated[$field] = $value;
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function value(array $data, string $field): mixed
    {
        if (! array_key_exists($field, $data)) {
            return null;
        }

        return $data[$field];
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    private static function parseRule(string $rule): array
    {
        if (! str_contains($rule, ':')) {
            return [$rule, null];
        }

        [$name, $parameter] = explode(':', $rule, 2);

        return [$name, $parameter];
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $replace
     */
    private static function addError(array &$errors, array $messages, string $field, string $rule, array $replace = []): void
    {
        $key = $field . '.' . $rule;
        $message = $messages[$key] ?? $messages[$field] ?? self::defaultMessage($field, $rule, $replace);

        foreach ($replace as $search => $value) {
            $message = str_replace('{' . $search . '}', $value, $message);
        }

        $errors[$field][] = $message;
    }

    /**
     * @param  array<string, string>  $replace
     */
    private static function defaultMessage(string $field, string $rule, array $replace): string
    {
        return match ($rule) {
            'required' => "El campo {$field} es obligatorio.",
            'string' => "El campo {$field} debe ser una cadena de texto.",
            'max' => isset($replace['n'])
                ? "El campo {$field} no debe superar {$replace['n']}."
                : "El campo {$field} excede el tamaño máximo permitido.",
            'in' => "El campo {$field} seleccionado no es válido.",
            'date' => "El campo {$field} no es una fecha válida.",
            'file' => "El campo {$field} debe ser un archivo válido.",
            'mimes' => isset($replace['list'])
                ? "El campo {$field} debe ser un archivo de tipo: {$replace['list']}."
                : "El tipo de archivo del campo {$field} no es válido.",
            default => "El campo {$field} no es válido.",
        };
    }
}
