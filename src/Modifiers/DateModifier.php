<?php
namespace MIA3\Import\Modifiers;

trait DateModifier
{
    /**
     * @param $string
     * @param $inputFormat
     * @param null $outputFormat
     * @return \DateTime|null|string
     */
    public function date($string, $inputFormat, $outputFormat = NULL) {
        $date = \DateTime::createFromFormat($inputFormat, $string);
        if ($outputFormat !== NULL) {
            if ($date instanceof \DateTime) {
                return $date->format(($outputFormat));
            }
            return NULL;
        }
        return $date;
    }
}