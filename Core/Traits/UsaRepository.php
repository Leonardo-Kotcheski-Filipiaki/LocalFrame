<?php

namespace Core\Traits;

use Core\Essentials\Repository;

/**
 * Fornece persistência local via arquivos .txt (Repository).
 * Requer USE_DATABASE=false no .env para funcionar corretamente.
 * 
 * Uso:
 *   class Produto extends ClasseBase { use UsaRepository; }
 */
trait UsaRepository
{
    /**
     * Instância singleton do Repository para esta classe.
     */
    protected static function repo(): Repository
    {
        static $repository = null;
        if ($repository === null) {
            $repository = new Repository();
        }
        return $repository;
    }

    /**
     * Salva (ou atualiza) o objeto atual no repositório local.
     */
    public function salvar(): bool
    {
        return static::repo()->atualizarLista($this);
    }

    /**
     * Remove o objeto pelo ID fornecido ou pelo próprio ID da instância.
     */
    public function remover(?string $id = null): bool
    {
        $idRemover = $id ?? ($this->id ?? null);
        if (!$idRemover) return false;

        return static::repo()->removerPorId($idRemover, static::class);
    }

    /**
     * Busca um objeto por ID no repositório local.
     */
    public static function buscar(string $id): ?static
    {
        return static::repo()->buscarPorId($id, static::class);
    }

    /**
     * Retorna todos os objetos do repositório local.
     */
    public static function buscarTodos(): array
    {
        return static::repo()->obterLista(static::class);
    }
}
