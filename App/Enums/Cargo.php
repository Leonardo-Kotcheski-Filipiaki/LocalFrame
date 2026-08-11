<?php


namespace App\Enums;

enum Cargo: string
{
    case GERENTE = "Gerente";
    case VENDEDOR = "Vendedor";

    public static function getCargos(): array
    {
        return array_map(fn($cargo) => $cargo->value, self::cases());
    }
}