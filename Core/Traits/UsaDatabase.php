<?php

namespace Core\Traits;

use Core\Essentials\Database;
use Core\Essentials\ExceptionHandler;
use Core\Notations\Table;
use ReflectionClass;

/**
 * Fornece uma interface fluente de instância para construção de queries SQL via Database.
 * Requer USE_DATABASE=true no .env para funcionar corretamente.
 *
 * Uso:
 *   class Pedido extends ClasseBase { use UsaDatabase; }
 *
 *   // Fluent query:
 *   $pedido->select('*')->from('pedidos')->where('status = "ativo"')->orderByDesc('criado_em')->get();
 *
 *   // Persistência:
 *   $pedido->salvarNoBanco();
 *   $pedido->removerNoBanco();
 *   Pedido::buscarNoBanco('uuid-123');
 *   Pedido::buscarTodosNoBanco();
 */
trait UsaDatabase
{
    /**
     * Query acumulada da instância atual.
     */
    private string $_query = '';

    /**
     * Bindings para prepared statements.
     */
    private array $_bindings = [];

    // -------------------------------------------------------------------------
    // Fluent Query Builder (instância)
    // -------------------------------------------------------------------------

    public function select(string|array $colunas = '*'): static
    {
        if (is_array($colunas)) {
            $colunas = implode(', ', $colunas);
        }
        $this->_query .= "SELECT $colunas ";
        return $this;
    }

    public function from(string $tabela): static
    {
        $this->_query .= "FROM $tabela ";
        return $this;
    }

    public function where(string $condicao, array $bindings = []): static
    {
        $this->_query .= "WHERE $condicao ";
        $this->_bindings = array_merge($this->_bindings, $bindings);
        return $this;
    }

    public function orderByDesc(string $coluna): static
    {
        $this->_query .= "ORDER BY $coluna DESC ";
        return $this;
    }

    public function orderByAsc(string $coluna): static
    {
        $this->_query .= "ORDER BY $coluna ASC ";
        return $this;
    }

    public function groupBy(string $coluna): static
    {
        $this->_query .= "GROUP BY $coluna ";
        return $this;
    }

    public function having(string $condicao): static
    {
        $this->_query .= "HAVING $condicao ";
        return $this;
    }

    public function limit(int $limite): static
    {
        $this->_query .= "LIMIT $limite ";
        return $this;
    }

    /**
     * Executa a query construída e retorna todos os resultados como array.
     * Limpa a query após a execução.
     */
    public function get(): array|null
    {
        try {
            $db = Database::instance();
            $stmt = $db->prepare(trim($this->_query));
            $stmt->execute($this->_bindings);
            return $stmt->fetchAll();
        } catch (\Throwable $th) {
            error_log('[UsaDatabase::get] ' . $th->getMessage());
            return null;
        } finally {
            $this->_query = '';
            $this->_bindings = [];
        }
    }

    /**
     * Executa a query e retorna apenas o primeiro resultado.
     */
    public function first(): array|null
    {
        $this->limit(1);
        $resultados = $this->get();
        return $resultados[0] ?? null;
    }

    // -------------------------------------------------------------------------
    // Persistência no banco (convenientes para o CRUD básico)
    // -------------------------------------------------------------------------

    /**
     * Salva (INSERT ou UPDATE) a instância atual no banco de dados.
     * A classe deve ter a anotação #[Table(table: "nome_tabela")].
     */
    public function salvarNoBanco(): bool
    {
        $reflection = new ReflectionClass(static::class);

        $dados = $this->_extrairDados($reflection);
        try {
            //Salvar dados no banco de forma a usar notations e tpt
            foreach ($dados as $key => $value) {
                var_dump($key, $value);
            }
            
            die;
            $db   = Database::instance();
            $stmt = $db->prepare("SELECT id FROM $tableName WHERE id = ?");
            $stmt->execute([$this->getId()]);
            $existe = $stmt->fetch();

            if ($existe) {
                $dadosUpdate = $dados;
                unset($dadosUpdate['id']);
                return Database::update($tableName, $dadosUpdate, 'id = ?', [$this->getId()]);
            }

            return Database::insert($tableName, $dados);
        } catch (\Throwable $th) {
            error_log('[UsaDatabase::salvarNoBanco] ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Remove a instância (ou um ID específico) do banco de dados.
     */
    public function removerNoBanco(?string $id = null): bool
    {
        $idRemover = $id ?? ($this->id ?? null);
        if (!$idRemover) return false;

        $reflection = new ReflectionClass(static::class);
        $tableName  = $this->_resolverTabela($reflection);

        if ($tableName === null) return false;

        try {
            $db   = Database::instance();
            $stmt = $db->prepare("DELETE FROM $tableName WHERE id = ?");
            return $stmt->execute([$idRemover]);
        } catch (\Throwable $th) {
            error_log('[UsaDatabase::removerNoBanco] ' . $th->getMessage());
            return false;
        }
    }

    /**
     * Busca um registro no banco por ID e retorna um array associativo.
     */
    public static function buscarNoBanco(string $id): array|null
    {
        $reflection = new ReflectionClass(static::class);
        $instance   = new static(...self::_argumentosFicticios($reflection));
        $tableName  = $instance->_resolverTabela($reflection);

        if ($tableName === null) return null;

        try {
            $db   = Database::instance();
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch();
            return $resultado ?: null;
        } catch (\Throwable $th) {
            error_log('[UsaDatabase::buscarNoBanco] ' . $th->getMessage());
            return null;
        }
    }

    /**
     * Retorna todos os registros da tabela como array de arrays associativos.
     */
    public static function buscarTodosNoBanco(): array|null
    {
        $reflection = new ReflectionClass(static::class);
        $tableName  = self::_resolverTabelaEstatico($reflection);

        if ($tableName === null) return [];

        try {
            $db   = Database::instance();
            $stmt = $db->prepare("SELECT * FROM $tableName");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Helpers internos
    // -------------------------------------------------------------------------

    /**
     * Resolve o nome da tabela a partir da anotação #[Table].
     */
    private function _resolverTabela(ReflectionClass $reflection): ?string
    {
        return self::_resolverTabelaEstatico($reflection);
    }

    private static function _resolverTabelaEstatico(ReflectionClass $reflection): ?string
    {
        $attributes = $reflection->getAttributes(Table::class);
        if (empty($attributes)) {
            error_log('[UsaDatabase] A classe ' . $reflection->getName() . ' não possui a anotação #[Table].');
            return null;
        }
        return $attributes[0]->newInstance()->getTable();
    }

    /**
     * Extrai os dados da instância como array associativo para INSERT/UPDATE.
     */
    private function _extrairDados(ReflectionClass $reflection): array
    {
        $dados       = [];
        $propriedades = $reflection->getProperties();

        foreach ($propriedades as $prop) {
            // Ignora propriedades internas da trait
            if (in_array($prop->getName(), ['_query', '_bindings'])) continue;
            if (!empty($prop->getAttributes(\Core\Notations\Ignorar::class))) continue;

            $nome   = $prop->getName();
            $getter = 'get' . ucfirst($nome);
            $valor  = null;

            if (method_exists($this, $getter)) {
                $valor = $this->$getter();
            } elseif ($prop->isInitialized($this)) {
                $valor = $prop->getValue($this);
            }

            if ($valor instanceof \BackedEnum)  $valor = strtolower($valor->value);
            elseif ($valor instanceof \UnitEnum) $valor = strtolower($valor->name);
            elseif (is_object($valor) || is_array($valor)) $valor = json_encode($valor, JSON_UNESCAPED_UNICODE);

            $dados[strtolower($prop->getDeclaringClass()->getShortName())][$nome] = $valor;
        }

        return $dados;
    }
}
