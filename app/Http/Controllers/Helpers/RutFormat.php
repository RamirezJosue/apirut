<?php

namespace App\Http\Controllers\Helpers;
use App\Http\Controllers\Exceptions\InvalidRutException;

class RutFormat
{
    public $rut;
    public $number;
    public $code;

    public function __construct($rut)
    {
        $this -> setRUT($rut);
    }
    private function setRUT($rut)
    {
        $this->rut = $this->clean($rut);
        list($this->number, $this->code) = explode('-', $this->rut);

        if (!$this->isValid()) {
            throw new InvalidRutException;
        }
    }

    private function isValid()
    {
        return strtoupper($this->code) == $this->getCodeFromNumber($this->number);
    }

    private function getCodeFromNumber($number)
    {
        $s = 1;
        for ($m = 0; $number!= 0; $number /= 10) {
            $s = ($s + $number % 10 * (9 - $m++ %6) ) % 11;
        }
        return chr($s ? $s + 47 : 75);
    }

    private function clean($rut)
    {
        list($number, $code) = explode('-', $rut);
        $number = ltrim(trim($number), '0');

        $numberClean = array_map(function($char) {
            return is_numeric($char) ? $char : null;
        }, str_split($number));

        return  join('', $numberClean) . '-' . strtoupper($code);
    }
}
