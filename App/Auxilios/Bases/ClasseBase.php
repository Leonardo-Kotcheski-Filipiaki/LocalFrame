<?php

namespace App\Auxilios\Bases;

use App\Auxilios\Essentials\Repository;

abstract class ClasseBase
{
    protected static function repo(): Repository
    {
        static $repository = null;
        if ($repository === null) {
            $repository = new Repository();
        }
        return $repository;
    }
    public function salvar(): bool
    {
        return $this->repo()->atualizarLista($this);
    }

    public function remover(?string $id = null): bool
    {
        $idRemover = $id ?? ($this->id ?? null);
        if (!$idRemover) return false;

        return static::repo()->removerPorId($idRemover, static::class);
    }

    public static function buscar(string $id): ?static
    {
        return static::repo()->buscarPorId($id, static::class);
    }

    public static function buscarTodos(): array
    {
        return static::repo()->obterLista(static::class);
    }
}