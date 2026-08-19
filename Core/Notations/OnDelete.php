<?php

namespace Core\Notations;

use Attribute;

/**
 * Marca o que deve acontecer ao que a classe é excluída
 * ao salvar/carregar do arquivo .txt
 * 
 * Uso: #[OnDelete] acima da propriedade
 */
#[Attribute(Attribute::TARGET_CLASS)]
class OnDelete
{
    public function __construct(public bool $excluirHierarquia)
    {
    }

    public function getExcluirHierarquia() : bool
    {
        return $this->excluirHierarquia;
    }
}
