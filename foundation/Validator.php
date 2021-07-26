<?php


namespace SquareMvc\Foundation;

use Illuminate\Database\Capsule\Manager as Capsule;
use Valitron\Validator as ValidationValidator;

class Validator
{
    /**
     * @param array $data
     * @return ValidationValidator
     */
    public static function get(array $data): ValidationValidator
    {
        $validator = new ValidationValidator(
            data: $data,
            lang: 'fr'
        );

        $validator->labels(require ROOT
            . DIRECTORY_SEPARATOR . 'resources'
            . DIRECTORY_SEPARATOR . 'lang'
            . DIRECTORY_SEPARATOR . 'validation.php');

        static::addCustomRules($validator);

        return $validator;
    }

    /**
     * @param ValidationValidator $validator
     */
    protected static function addCustomRules(ValidationValidator $validator): void
    {
        $validator->addRule('unique', function (string $field, mixed $value, array $params, array $fields) {
            return !Capsule::table($params[1])->where($params[0], $value)->exists();
        }, '{field} est invalide');

        $validator->addRule('password', function (string $field, mixed $value, array $params, array $fields) {
            $user = Authentication::get();
            return password_verify($value, $user->password);
        }, '{field} est erroné');
    }
}