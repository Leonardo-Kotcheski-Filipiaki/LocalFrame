<?php

namespace App\Enums;

enum TipoCompra : string
{
    case D = "Débito"; //Dinheiro Cliente
    case C = "Crédito"; //Parcelado
    case CR = "Crediário"; // Loja (Nível)

    public static function getStringTipos() : array
    {
        $tipos = [];
        foreach (self::cases() as $tipo) {
            $tipos[] = $tipo->value;
        }
        return $tipos;
    }
}