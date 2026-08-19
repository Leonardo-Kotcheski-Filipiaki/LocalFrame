<?php

namespace Core\Notations;

use Attribute;

/**
 * Marca qual tabela depende a classe
 * 
 * Uso: #[Depends] acima da classe
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Depends
{
    public function __construct(public string $table, public string|null $idColumn, public string|null $localIdColumn)
    {
    }

    public function getTable() : string
    {
        return $this->table;
    }

    public function getIdColumn() : string|null
    {
        return $this->idColumn;
    }

    public function getLocalIdColumn() : string|null
    {
        return $this->localIdColumn;
    }
}
