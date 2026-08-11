<?php

namespace App\Auxilios;

use App\Classes\Cliente;
use App\Classes\Compra;
use App\Classes\Divida;
use App\Classes\Funcionario;
use App\Classes\Produto;
use App\Classes\Venda;
use App\Enums\Nivel;
use ReflectionClass;
use ReflectionNamedType;

class Repository
{
    private array $listaClientes = [];
    private array $listaFuncionario = [];
    private array $listaProduto = [];
    private array $listaVendas = [];
    private array $listaNiveis;

    private string $diretorio;
    private string $delimitador = ';';

    public function __construct(string $diretorioStorage = __DIR__ . '/../database')
    {
        $this->diretorio = rtrim($diretorioStorage, '/') . '/';
        $this->garantirDiretorio();

        $this->listaNiveis = Nivel::getStringNiveis();

        $this->listaClientes = $this->carregarDoArquivo(Cliente::class);
        $this->listaFuncionario = $this->carregarDoArquivo(Funcionario::class);
        $this->listaProduto = $this->carregarDoArquivo(Produto::class);
    }

    public function atualizarLista(Cliente|Funcionario|Produto|Venda|Divida|Compra $classe): bool
    {
        try {
            $classeFqcn = $classe::class;
            $lista = &$this->obterReferenciaLista($classeFqcn);

            $existe = false;
            foreach ($lista as $index => $item) {
                if ($item->getId() == $classe->getId()) {
                    $lista[$index] = $classe;
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                $lista[] = $classe;
            }

            $this->salvarEmArquivo($classeFqcn, $lista);
            return true;
        } catch (\Throwable $th) {
            Render::erro("Um erro ocorreu ao salvar as informações no banco!", null, 500);
            return false;
        }
    }

    public function getListaClientes(): array
    {
        return $this->listaClientes;
    }

    public function getListaFuncionario(): array
    {
        return $this->listaFuncionario;
    }

    public function getListaProduto(): array
    {
        return $this->listaProduto;
    }

    public function getListaNiveis(): array
    {
        return $this->listaNiveis;
    }

    public function getListaVendas(): array
    {
        return $this->listaVendas;
    }

    private function salvarEmArquivo(string $classeFqcn, array $lista): void
    {
        $caminhoArquivo = $this->getCaminhoArquivo($classeFqcn);
        $reflection = new ReflectionClass($classeFqcn);
        $propriedades = $reflection->getProperties();

        $linhas = [];

        // 1. Cabeçalho (Colunas)
        $colunas = array_map(fn($p) => $p->getName(), $propriedades);
        $linhas[] = implode($this->delimitador, $colunas);

        // 2. Linhas (Dados)
        foreach ($lista as $objeto) {
            $valores = [];
            foreach ($propriedades as $propriedade) {
                $nomeAtributo = $propriedade->getName();
                $metodoGetter = 'get' . ucfirst($nomeAtributo);
                $valor = null;

                if (method_exists($objeto, $metodoGetter)) {
                    $valor = $objeto->$metodoGetter();
                } elseif ($propriedade->isInitialized($objeto)) {
                    $valor = $propriedade->getValue($objeto);
                }

                $valores[] = $this->formatarValorParaTxt($valor);
            }
            $linhas[] = implode($this->delimitador, $valores);
        }
        
        file_put_contents($caminhoArquivo, implode(PHP_EOL, $linhas));
    }

    private function carregarDoArquivo(string $classeFqcn): array
    {
        $caminhoArquivo = $this->getCaminhoArquivo($classeFqcn);

        if (!file_exists($caminhoArquivo)) {
            return [];
        }

        $linhas = file($caminhoArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($linhas)) {
            return [];
        }

        $colunas = explode($this->delimitador, array_shift($linhas));
        $reflection = new ReflectionClass($classeFqcn);
        $objetos = [];

        foreach ($linhas as $linha) {
            $valores = explode($this->delimitador, $linha);
            $objeto = $reflection->newInstanceWithoutConstructor();

            foreach ($colunas as $index => $nomeColuna) {
                if ($reflection->hasProperty($nomeColuna)) {
                    $propriedade = $reflection->getProperty($nomeColuna);
                    $valorRaw = $valores[$index] ?? null;

                    $valorConvertido = $this->converterValorDoTxt($valorRaw, $propriedade->getType());
                    $propriedade->setValue($objeto, $valorConvertido);
                }
            }

            $objetos[] = $objeto;
        }

        return $objetos;
    }

    private function formatarValorParaTxt(mixed $valor): string
    {
        if ($valor === null) return '';
        if (is_bool($valor)) return $valor ? '1' : '0';
        if ($valor instanceof \BackedEnum) return (string) $valor->value;
        if ($valor instanceof \UnitEnum) return $valor->name;
        if (is_object($valor)) return json_encode($this->serializarObjeto($valor), JSON_UNESCAPED_UNICODE);
        if (is_array($valor)) return json_encode($this->serializarArray($valor), JSON_UNESCAPED_UNICODE);
        return (string) $valor;
    }

    /**
     * Serializa um objeto para um array associativo, incluindo __class para reconstrução.
     */
    private function serializarObjeto(object $objeto): array
    {
        if ($objeto instanceof \BackedEnum) return ['__enum' => $objeto::class, 'value' => $objeto->value];
        if ($objeto instanceof \UnitEnum) return ['__enum' => $objeto::class, 'name' => $objeto->name];

        $reflection = new ReflectionClass($objeto);
        $dados = ['__class' => $objeto::class];

        // Percorrer todas as propriedades, incluindo as herdadas
        $todasPropriedades = $this->obterTodasPropriedades($reflection);

        foreach ($todasPropriedades as $propriedade) {
            if (!$propriedade->isInitialized($objeto)) continue;

            $nomeAtributo = $propriedade->getName();
            $metodoGetter = 'get' . ucfirst($nomeAtributo);
            $valor = null;

            if (method_exists($objeto, $metodoGetter)) {
                $valor = $objeto->$metodoGetter();
            } else {
                $valor = $propriedade->getValue($objeto);
            }

            if ($valor === null) {
                $dados[$nomeAtributo] = null;
            } elseif (is_bool($valor)) {
                $dados[$nomeAtributo] = $valor;
            } elseif ($valor instanceof \BackedEnum) {
                $dados[$nomeAtributo] = ['__enum' => $valor::class, 'value' => $valor->value];
            } elseif ($valor instanceof \UnitEnum) {
                $dados[$nomeAtributo] = ['__enum' => $valor::class, 'name' => $valor->name];
            } elseif (is_object($valor)) {
                $dados[$nomeAtributo] = $this->serializarObjeto($valor);
            } elseif (is_array($valor)) {
                $dados[$nomeAtributo] = $this->serializarArray($valor);
            } else {
                $dados[$nomeAtributo] = $valor;
            }
        }

        return $dados;
    }

    /**
     * Serializa um array, tratando objetos dentro dele recursivamente.
     */
    private function serializarArray(array $array): array
    {
        $resultado = [];
        foreach ($array as $chave => $item) {
            if (is_object($item)) {
                $resultado[$chave] = $this->serializarObjeto($item);
            } elseif (is_array($item)) {
                $resultado[$chave] = $this->serializarArray($item);
            } else {
                $resultado[$chave] = $item;
            }
        }
        return $resultado;
    }

    /**
     * Desserializa um array associativo de volta para um objeto da classe indicada em __class.
     */
    private function desserializarObjeto(array $dados): object
    {
        // Trata enums
        if (isset($dados['__enum'])) {
            $enumClass = $dados['__enum'];
            if (isset($dados['value']) && is_subclass_of($enumClass, \BackedEnum::class)) {
                return $enumClass::from($dados['value']);
            }
            if (isset($dados['name'])) {
                return constant("{$enumClass}::{$dados['name']}");
            }
        }

        $classeFqcn = $dados['__class'];
        $reflection = new ReflectionClass($classeFqcn);
        $objeto = $reflection->newInstanceWithoutConstructor();

        $todasPropriedades = $this->obterTodasPropriedades($reflection);

        foreach ($todasPropriedades as $propriedade) {
            $nomeAtributo = $propriedade->getName();
            if (!array_key_exists($nomeAtributo, $dados)) continue;

            $valorRaw = $dados[$nomeAtributo];
            $tipo = $propriedade->getType();

            $valorConvertido = $this->converterValorDesserializado($valorRaw, $tipo);
            $propriedade->setValue($objeto, $valorConvertido);
        }

        return $objeto;
    }

    /**
     * Converte um valor desserializado (do JSON) para o tipo correto da propriedade.
     */
    private function converterValorDesserializado(mixed $valor, ?\ReflectionType $tipo): mixed
    {
        // Se é um array com __class ou __enum, reconstruir o objeto
        if (is_array($valor) && (isset($valor['__class']) || isset($valor['__enum']))) {
            return $this->desserializarObjeto($valor);
        }

        // Se é um array, verificar se contém objetos dentro
        if (is_array($valor)) {
            return $this->desserializarArray($valor);
        }

        // Valor null
        if ($valor === null) {
            return null;
        }

        // Tipos primitivos - usar ReflectionType para conversão
        if ($tipo instanceof ReflectionNamedType) {
            $nomeTipo = $tipo->getName();

            if (enum_exists($nomeTipo)) {
                if (is_subclass_of($nomeTipo, \BackedEnum::class)) {
                    return $nomeTipo::tryFrom($valor);
                }
                return defined("{$nomeTipo}::{$valor}") ? constant("{$nomeTipo}::{$valor}") : null;
            }

            return match ($nomeTipo) {
                'int' => (int) $valor,
                'float' => (float) $valor,
                'bool' => (bool) $valor,
                'string' => (string) $valor,
                default => $valor,
            };
        }

        return $valor;
    }

    /**
     * Desserializa um array, reconstruindo objetos encontrados dentro.
     */
    private function desserializarArray(array $array): array
    {
        $resultado = [];
        foreach ($array as $chave => $item) {
            if (is_array($item) && (isset($item['__class']) || isset($item['__enum']))) {
                $resultado[$chave] = $this->desserializarObjeto($item);
            } elseif (is_array($item)) {
                $resultado[$chave] = $this->desserializarArray($item);
            } else {
                $resultado[$chave] = $item;
            }
        }
        return $resultado;
    }

    /**
     * Obtém todas as propriedades de uma classe, incluindo as de classes pai.
     * @return \ReflectionProperty[]
     */
    private function obterTodasPropriedades(ReflectionClass $reflection): array
    {
        $propriedades = $reflection->getProperties();
        return $propriedades;
    }

    private function converterValorDoTxt(?string $valor, ?\ReflectionType $tipo): mixed
    {
        $permiteNulo = $tipo ? $tipo->allowsNull() : true;

        // Trata valores vazios respeitando se a propriedade aceita null ou não
        if ($valor === null || $valor === '') {
            if ($permiteNulo) {
                return null;
            }

            if ($tipo instanceof ReflectionNamedType) {
                return match ($tipo->getName()) {
                    'int' => 0,
                    'float' => 0.0,
                    'bool' => false,
                    'array' => [],
                    'string' => '',
                    default => '',
                };
            }

            return '';
        }

        if (!$tipo instanceof ReflectionNamedType) {
            return $valor;
        }

        $nomeTipo = $tipo->getName();

        // Trata Enums
        if (enum_exists($nomeTipo)) {
            if (is_subclass_of($nomeTipo, \BackedEnum::class)) {
                return $nomeTipo::tryFrom($valor);
            }
            return defined("{$nomeTipo}::{$valor}") ? constant("{$nomeTipo}::{$valor}") : null;
        }

        // Se o tipo é uma classe conhecida, tentar desserializar do JSON
        if (class_exists($nomeTipo) && !in_array($nomeTipo, ['int', 'float', 'bool', 'string', 'array'])) {
            $dados = json_decode($valor, true);
            if (is_array($dados)) {
                if (isset($dados['__class']) || isset($dados['__enum'])) {
                    return $this->desserializarObjeto($dados);
                }
                // Dados legados sem __class: criar instância vazia e popular propriedades disponíveis
                $dados['__class'] = $nomeTipo;
                return $this->desserializarObjeto($dados);
            }
        }

        // Se o tipo é array, verificar se contém objetos serializados
        if ($nomeTipo === 'array') {
            $dados = json_decode($valor, true);
            if (is_array($dados)) {
                return $this->desserializarArray($dados);
            }
            return [];
        }

        // Conversão de tipos primitivos
        return match ($nomeTipo) {
            'int' => (int) $valor,
            'float' => (float) $valor,
            'bool' => (bool) $valor || $valor === '1',
            'string' => (string) $valor,
            default => $valor,
        };
    }

    private function &obterReferenciaLista(string $classeFqcn): array
    {
        switch ($classeFqcn) {
            case Cliente::class:
                return $this->listaClientes;
            case Funcionario::class:
                return $this->listaFuncionario;
            default:
                return $this->listaProduto;
        }
    }

    private function getCaminhoArquivo(string $classeFqcn): string
    {
        $reflection = new ReflectionClass($classeFqcn);
        return $this->diretorio . $reflection->getShortName() . '.txt';
    }

    private function garantirDiretorio(): void
    {
        if (!is_dir($this->diretorio)) {
            mkdir($this->diretorio, 0755, true);
        }
    }

    public function buscarPorId(string $id, string $classeFqcn): ?object
    {
        $lista = &$this->obterReferenciaLista($classeFqcn);

        foreach ($lista as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return null;
    }

    public function removerPorId(string $id, string $classeFqcn): bool
    {
        $lista = &$this->obterReferenciaLista($classeFqcn);

        foreach ($lista as $index => $item) {
            if ($item->getId() == $id) {
                unset($lista[$index]);
                $this->salvarEmArquivo($classeFqcn, $lista);
                return true;
            }
        }

        return false;
    }
}