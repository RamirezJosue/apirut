<?php
namespace App\Http\Controllers\Exceptions;


class InvalidRutException
{
    protected $message = "El código de verificación no coincidía con el RUT. El RUT y el código deben estar delimitados con -";
}
