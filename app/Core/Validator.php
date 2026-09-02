<?php
namespace App\Core;

/**
 * Unified Enterprise Validation & Sanitization Engine
 * Standardizes input validation across all controllers with Thai localized messages
 */
class Validator {
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $sanitized = [];

    protected static array $customMessages = [
        'required' => 'กรุณาระบุข้อมูล :field',
        'email' => 'รูปแบบอีเมลไม่ถูกต้อง (:field)',
        'thai_phone' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็นเบอร์ 10 หลัก เช่น 0812345678)',
        'numeric' => ':field ต้องเป็นตัวเลขเท่านั้น',
        'integer' => ':field ต้องเป็นจำนวนเต็มเท่านั้น',
        'min' => ':field ต้องมีค่าหรือความยาวไม่น้อยกว่า :param',
        'max' => ':field ต้องมีค่าหรือความยาวไม่เกิน :param',
        'in' => ':field ต้องเป็นค่าที่กำหนดไว้เท่านั้น (:param)',
        'coordinates' => ':field พิกัดละติจูด/ลองจิจูดอยู่นอกขอบเขตที่ถูกต้อง'
    ];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    /**
     * Factory method for clean chainable syntax
     */
    public static function make(array $data, array $rules): self {
        return new self($data, $rules);
    }

    /**
     * Run validation rules against data
     */
    protected function validate(): void {
        foreach ($this->rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            // Sanitize string inputs (trim, strip null bytes)
            if (is_string($value)) {
                $value = str_replace(chr(0), '', trim($value));
                $this->sanitized[$field] = $value;
            } else {
                $this->sanitized[$field] = $value;
            }

            foreach ($ruleList as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleParam) = explode(':', $rule, 2);
                }

                $method = 'validate' . str_replace(' ', '', ucwords(str_replace('_', ' ', $ruleName)));
                if (method_exists($this, $method)) {
                    $passed = $this->$method($field, $value, $ruleParam);
                    if (!$passed) {
                        $this->addError($field, $ruleName, $ruleParam);
                        break; // Stop at first failed rule for this field
                    }
                }
            }
        }
    }

    // --- Validation Rules ---

    protected function validateRequired(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') {
            return false;
        }
        if (is_array($value) && empty($value)) {
            return false;
        }
        return true;
    }

    protected function validateEmail(string $field, $value, ?string $param): bool {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateThaiPhone(string $field, $value, ?string $param): bool {
        if (empty($value)) return true;
        $cleaned = preg_replace('/[^0-9]/', '', (string)$value);
        return preg_match('/^0[689]\d{8}$/', $cleaned) === 1 || preg_match('/^0[2-57]\d{7,8}$/', $cleaned) === 1;
    }

    protected function validateNumeric(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        return is_numeric($value);
    }

    protected function validateInteger(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateMin(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        $min = (float)$param;
        if (is_numeric($value)) {
            return (float)$value >= $min;
        }
        if (is_string($value)) {
            return mb_strlen($value, 'UTF-8') >= (int)$min;
        }
        if (is_array($value)) {
            return count($value) >= (int)$min;
        }
        return true;
    }

    protected function validateMax(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        $max = (float)$param;
        if (is_numeric($value)) {
            return (float)$value <= $max;
        }
        if (is_string($value)) {
            return mb_strlen($value, 'UTF-8') <= (int)$max;
        }
        if (is_array($value)) {
            return count($value) <= (int)$max;
        }
        return true;
    }

    protected function validateIn(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        $allowed = array_map('trim', explode(',', $param ?? ''));
        return in_array((string)$value, $allowed, true);
    }

    protected function validateCoordinates(string $field, $value, ?string $param): bool {
        if ($value === null || $value === '') return true;
        if (!is_numeric($value)) return false;
        $val = (float)$value;
        if ($param === 'lat') {
            return $val >= -90.0 && $val <= 90.0;
        } elseif ($param === 'lng') {
            return $val >= -180.0 && $val <= 180.0;
        }
        return true;
    }

    // --- Helpers ---

    protected function addError(string $field, string $rule, ?string $param): void {
        $msg = self::$customMessages[$rule] ?? "ข้อมูล {$field} ไม่ถูกต้อง";
        $msg = str_replace(':field', $field, $msg);
        if ($param !== null) {
            $msg = str_replace(':param', $param, $msg);
        }
        $this->errors[$field][] = $msg;
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !$this->passes();
    }

    public function errors(): array {
        return $this->errors;
    }

    public function first(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    public function allErrors(): array {
        $all = [];
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $err) {
                $all[] = $err;
            }
        }
        return $all;
    }

    public function validated(): array {
        return $this->sanitized;
    }
}
