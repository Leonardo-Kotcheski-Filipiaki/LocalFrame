<?php

namespace App\Enums;

enum Nivel : string
{
    case BRONZE = "Bronze";
    case PRATA = "Prata";
    case OURO = "Ouro";
    case BLACK = "Black";

    public function getCreditoPorNivel() : float
    {
        return array (
                Nivel::BRONZE->value => 1500.00,
                Nivel::PRATA->value => 2500.00,
                Nivel::OURO->value => 5000.00,
                Nivel::BLACK->value => 10000.00
            )[$this->value];
    }

    public static function getStringNiveis() : array
    {
        return array_column(self::cases(), 'value');
    }
}