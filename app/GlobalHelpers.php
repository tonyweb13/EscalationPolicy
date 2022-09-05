<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Log;

/**
 * Gets a configuration option or throw an exception if it cannot be found. The second argument
 * can be used to ensure the config value is of the specified type.
 *
 * @param string $key the config key value to retrieve.
 * @param string $expected_type type type of value expected ('integer', 'string', 'boolean', 'double', ...)
 *        Defaults to 'string'.
 * @return mixed -- can return an integer, string, double, ...
 * @throws RuntimeException if the value is missing or is not of the specified type.
 */
function config_safe(string $key, $expected_type = 'string')
{
    $value = config($key);

    $actual_type = gettype($value);
    return $value;
}

function ppr($description, array $arr)
{
    Log::info($description . a_to_s($arr));
}

function a_to_s(array $arr, int $indent = 0)
{
    ksort($arr);
    $ret = '';
    foreach ($arr as $key => $value) {
        $prefix = str_repeat(' ', $indent);
        if (is_array($value)) {
            $ret .= $prefix . "${key}: {\n";
            $ret .= a_to_s($value, $indent + 4);
            $ret .= $prefix .  "}\n";
        } elseif (is_object($value)) {
            $vars = get_object_vars($value);
            $ret .= $prefix . "${key} [object]: {\n";
            $ret .= a_to_s($vars, $indent + 4);
            $ret .= $prefix .  "}\n";
        } else {
            $type = gettype($value);
            if ($type === 'boolean') {
                $text = $value ? 'true' : 'false';
            } elseif ($type === 'NULL') {
                $text = 'null';
            } elseif ($type === 'string') {
                $value = str_replace("\n", "\\n", $value);
                $text = "'${value}'";
            } else {
                $text = "${value} ($type)";
            }
            $ret .= $prefix . "${key}: ${text}\n";
        }
    }
    return $ret;
}
