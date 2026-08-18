<?php

namespace Core\Bases;

/**
 * Classe base abstrata para todos os Models da aplicação.
 *
 * Por si só não possui comportamento de persistência.
 * Inclua as traits abaixo conforme a necessidade de cada Model:
 *
 *   use \Core\Traits\UsaRepository;   — persistência em arquivos locais (.txt)
 *   use \Core\Traits\UsaDatabase;     — queries fluentes via Database (PDO)
 *
 * Exemplo:
 *   class Produto extends ClasseBase { use UsaRepository; }
 *   class Pedido  extends ClasseBase { use UsaDatabase; }
 *   class Cliente extends ClasseBase { use UsaRepository, UsaDatabase; }
 */
abstract class ClasseBase {}