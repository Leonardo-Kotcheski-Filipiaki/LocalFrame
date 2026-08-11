<?php

namespace App\Enums;

enum TipoProduto: string
{
    case ELETRODOM = "Eletrodomésticos";
    case ELETRONICO = "Eletronicos";
    case FERRAMENTA = "Ferramentas";
    case UTILIDADES = "Utilidades";
    case DECO = "DECORAÇÕES";

    public static function getStringTipoProdutos() : array
    {
        return [
            self::ELETRODOM->value,
            self::ELETRONICO->value,
            self::FERRAMENTA->value,
            self::UTILIDADES->value,
            self::DECO->value
        ];
    }
}
