<?php

namespace App\Auxilios;

abstract class ClasseBase 
{
    public function salvar(): bool
    {
        return repo()->atualizarLista($this);
    }

    public function remover(?string $id = null): bool
    {
        $idRemover = $id ?? ($this->id ?? null);
        if (!$idRemover) return false;

        return repo()->removerPorId($idRemover, static::class);
    }

    public static function buscar(string $id): ?static
    {
        return repo()->buscarPorId($id, static::class);
    }

    public static function buscarTodos(): array
    {
        return repo()->obterLista(static::class);
    }
}