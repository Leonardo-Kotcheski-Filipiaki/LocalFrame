<?php

namespace Core\Traits;

use BackedEnum;
use Core\Essentials\Database;
use Core\Essentials\ExceptionHandler;
use Core\Notations\Depends;
use Core\Notations\Ignorar;
use Core\Notations\Table;
use Reflection;
use ReflectionClass;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use UnitEnum;

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
            ExceptionHandler::handle($th);
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

    public function salvarNoBanco(): bool
    {
        $reflection = new ReflectionClass(static::class);

        // 1. Montar a hierarquia (de pai para filho)
        $hierarquia = [];
        $currentClass = $reflection;
        while ($currentClass) {
            array_unshift($hierarquia, $currentClass);
            $currentClass = $currentClass->getParentClass();
        }

        // 2. Extrair dados agrupados pela classe de origem
        $dadosPorClasse = $this->_extrairDadosPorClasse($reflection);
        
        // 3. ID global: pega o atual ou gera um novo
        $idGlobal = $this->getId() ?: uniqid();

        try {
            $db = Database::instance();
            if ($db && !$db->inTransaction()) {
                $db->beginTransaction();
            }

            $idPai = null;

            // Percorre da base até a classe folha
            foreach ($hierarquia as $classRef) {
                $className = $classRef->getName();
                $tableName = self::_resolverTabelaEstatico($classRef);
                
                if (!$tableName) {
                    continue; // Pula se não for uma entidade mapeada
                }

                $dados = $dadosPorClasse[$className] ?? [];

                // Lida com a dependência (Foreign Key TPT)
                $dependsAttr = $classRef->getAttributes(\Core\Notations\Depends::class);
                if (!empty($dependsAttr)) {
                    $depends = $dependsAttr[0]->newInstance();
                    $localIdCol = $depends->getLocalIdColumn();
                    // Se depende de alguém e temos o ID pai, vinculamos
                    if ($localIdCol && $idPai) {
                        $dados[$localIdCol] = $idPai;
                    }
                }

                // Todas as tabelas da hierarquia recebem o mesmo ID base
                $dados['id'] = $idGlobal;

                $stmt = $db->prepare("SELECT id FROM $tableName WHERE id = ?");
                $stmt->execute([$dados['id']]);
                $existe = $stmt->fetch();

                if ($existe) {
                    $dadosUpdate = $dados;
                    unset($dadosUpdate['id']);
                    if (!empty($dadosUpdate)) {
                        Database::update($tableName, $dadosUpdate, 'id = ?', [$dados['id']]);
                    }
                } else {
                    Database::insert($tableName, $dados);
                }

                // Guarda o ID para a próxima tabela na hierarquia (se houver)
                $idPai = $dados['id'];
            }

            if ($db && $db->inTransaction()) {
                $db->commit();
            }

            // Atualiza a instância com o novo ID se foi gerado
            if (!$this->getId() && $reflection->hasProperty('id')) {
                $propId = $reflection->getProperty('id');
                $propId->setValue($this, $idGlobal);
            }

            return true;
        } catch (\Throwable $th) {
            $db = Database::instance();
            if ($db && $db->inTransaction()) {
                $db->rollBack();
            }
            ExceptionHandler::handle($th);
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
            ExceptionHandler::handle($th);
            return false;
        }
    }

    /**
     * Busca um registro no banco por ID e retorna um array associativo.
     */
    public static function buscarNoBanco(string $id): object|null
    {
        $reflection = new ReflectionClass(static::class);
        $novaInstancia   = $reflection->newInstanceWithoutConstructor();
        $tableName  = $novaInstancia->_resolverTabela($reflection);
        
        $hierarquia = [];
        $currentClass = $reflection;
        while ($currentClass) {
            if ($currentClass->getAttributes(Ignorar::class)){
                $currentClass = $currentClass->getParentClass();
                continue;
            } else {
                array_unshift($hierarquia, $currentClass);
                $currentClass = $currentClass->getParentClass();
            }
        }
        
        $join = "";
        for ($i = 1; $i < count($hierarquia); $i++) {
            $join .= " INNER JOIN " . strtolower($hierarquia[$i-1]->getShortName()) . " ON ";
            
            $join .= strtolower($hierarquia[$i-1]->getShortName()) . ".id = ";
            $idColumn = 'id';
            if ($hierarquia[$i]->getAttributes(Depends::class)){
                $idColumn = $hierarquia[$i]->getAttributes(Depends::class)[0]->newInstance()->getLocalIdColumn();
            }
            $join .= strtolower($hierarquia[$i]->getShortName()) . "." . $idColumn . " ";            
        }
        if ($tableName === null) return null;

        try {
            $db   = Database::instance();
            $stmt = $db->prepare("SELECT * FROM $tableName " . $join . "  WHERE " . $tableName . ".id = ?");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch();
            if ($resultado != null) {
                static::tratar($resultado, $reflection, $novaInstancia);
            }
            return $novaInstancia ?: null;
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
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
     * Extrai os dados da instância agrupados pelo nome da classe origem.
     */
    private function _extrairDadosPorClasse(ReflectionClass $reflection): array
    {
        $dados = [];
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

            $dados[$prop->getDeclaringClass()->getName()][$nome] = $valor;
        }

        return $dados;
    }

    /**
     * Trata o retorno dos dados
     * @param array $resultado
     * @param ReflectionClass $reflection
     * @param object $novaInstancia
     * @return void
     */
    private static function tratar(array $resultado, ReflectionClass $reflection, object &$novaInstancia) {
        $propriedades = $reflection->getProperties();
        foreach ($propriedades as $prop) {
            $nomeProp = $prop->getName();

            if (!array_key_exists($nomeProp, $resultado)) {
                continue;
            }
            
            $valor = $resultado[$prop->getName()];

            if (static::aPropriedadeEhEnum($prop)) {
                if ($valor === null) {
                    $prop->setValue($novaInstancia, null);
                    continue;
                }

                $classeEnum = $prop->getType()->getName();
                $instanciaEnum = null;

                if (is_subclass_of($classeEnum, BackedEnum::class)) {
                    $instanciaEnum = $classeEnum::tryFrom($valor)
                    ?? $classeEnum::tryFrom((int)$valor);
                } else {
                    $reflectionEnum = new ReflectionEnum($classeEnum);
                    
                    foreach ($reflectionEnum->getCases() as $case) {
                        if (strcasecmp($case->getName(), (string)$valor) === 0) {
                            $instanciaEnum = $case->getValue();
                            break;
                        }
                        }
                    }
                    if (isset($instanciaEnum)) {
                        $prop->setValue($novaInstancia, $instanciaEnum);
                    }
            } else {
                $prop->setValue($novaInstancia, $valor);
            }     
        }
    }
    /**
     * Verifica propriedade Enum
     * @param ReflectionProperty $prop
     * @return bool
     */
    private static function aPropriedadeEhEnum(ReflectionProperty $prop)  
    {
        $type = $prop->getType();

        if (!$type) 
        {
            return false;
        }

        if ($type instanceof ReflectionNamedType)
        {
            return !$type->isBuiltin() && enum_exists($type->getName());
        }

        if ($type instanceof ReflectionUnionType) 
        {
            foreach ($type->getTypes() as $namedType) {
                if ($namedType instanceof ReflectionNamedType && !$namedType->isBuiltin())
                {
                    if (enum_exists($namedType->getName()))
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
