<?php
namespace MIA3\Import\Modifiers;

trait TextModifier
{
    /**
     * @param $string
     * @return string
     */
    public function uppercase($string) {
        return strtoupper($string);
    }

    /**
     * @param $input
     * @param $delimiter
     * @return string
     */
    public function combine($input, $delimiter) {
        return implode($delimiter, $input);
    }

    /**
     * @param $input
     * @param $delimiter
     * @return array
     */
    public function split($input, $delimiter) {
        return explode($delimiter, $input);
    }

    /**
     * @param $input
     * @param $map
     * @param string $default
     * @return string
     */
    public function valueMap($input, $map, $default = 'DEFAULT VALUE: e9c66f63-10aa-436d-9c06-d4a2e3849860') {
        if (isset($map[$input])) {
            return $map[$input];
        }
        if ($default === 'DEFAULT VALUE: e9c66f63-10aa-436d-9c06-d4a2e3849860') {
            return $input;
        }
        return $default;
    }
}