<?php

namespace App\Auxilios;

use Attribute;

/**
 * Marca uma propriedade para ser ignorada pelo Repository
 * ao salvar/carregar do arquivo .txt
 * 
 * Uso: #[Ignorar] acima da propriedade
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Ignorar
{
}
