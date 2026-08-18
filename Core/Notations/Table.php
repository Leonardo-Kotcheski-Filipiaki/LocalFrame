<?php

namespace Core\Notations;

use Attribute;

/**
 * Marca qual tabela pertence a classe
 * 
 * Uso: #[Table] acima da classe
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(public string $table)
    {
    }

    public function getTable() : string
    {
        return $this->table;
    }
}
