<?php

namespace App\Auxilios\Essentials;

use App\Auxilios\Essentials\Request;

class Router
{
    protected static array $rotas = [];

    public static function get(string $uri, $acao)
    {
        self::addRoute('GET', $uri, $acao);
    }

    public static function post(string $uri, $acao)
    {
        self::addRoute('POST', $uri, $acao);
    }

    public static function delete(string $uri, $acao)
    {
        self::addRoute('DELETE', $uri, $acao);
    }

    protected static function addRoute(string $metodo, string $uri, $acao)
    {
        self::$rotas[$metodo][$uri] = $acao;
    }

    public static function dispatch(string $uri, string $metodo)
    {
        try {
            $request = new Request();
        
            $uri = parse_url($uri, PHP_URL_PATH);

            if (!isset(self::$rotas[$metodo])) {
                http_response_code(404);
                echo "404 Not Found";
                return;
            }
            foreach (self::$rotas[$metodo] as $rota => $acao) {
                // Converte {param} para expressão regular
                $padrao = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $rota);
                $padrao = "#^" . $padrao . "$#";
                
                if (preg_match($padrao, $uri, $correspondencias)) {
                    $parametrosRota = array_filter($correspondencias, 'is_string', ARRAY_FILTER_USE_KEY);
                    $callable = null;
                    // 1. Ação é uma Closure / Função
                    if (is_callable($acao)) {
                        $callable = $acao;
                    }
                    // 2. Ação é um Array [Controller::class, 'metodo']
                    elseif (is_array($acao) && count($acao) === 2) {
                        $controlador = is_string($acao[0]) ? new $acao[0]() : $acao[0];
                        $callable = [$controlador, $acao[1]];
                    }
                    // 3. Ação é uma String no formato 'Controller@metodo'
                    elseif (is_string($acao) && strpos($acao, '@') !== false) {
                        list($classeControlador, $metodoControlador) = explode('@', $acao);
                        $namespaceControlador = "App\\Controller\\" . $classeControlador;
                        $controlador = new $namespaceControlador();
                        $callable = [$controlador, $metodoControlador];
                    }

                    // Se identificou um callable válido, resolve as dependências e executa
                    if ($callable) {
                        $argumentos = self::resolverParametros($callable, $parametrosRota, $request);
                        return call_user_func_array($callable, $argumentos);
                    }
                }
            }

            http_response_code(404);
            Render::render('erros/404');
            exit;
        } catch (\Throwable $th) {
            ExceptionHandler::handle($th);
        }
    }

    /**
     * Inspeciona os parâmetros do método/função e injeta $request ou parâmetros da URL
     */
    protected static function resolverParametros($callable, array $parametrosRota, Request $request): array
    {
        try{
            $reflection = is_array($callable) 
            ? new \ReflectionMethod($callable[0], $callable[1]) 
            : new \ReflectionFunction($callable);
        }catch(\ReflectionException $e){
            throw new \Exception("Erro ao refletir a função: " . $e->getMessage());
        }

        $argumentos = [];

        foreach ($reflection->getParameters() as $parametro) {
            $tipo = $parametro->getType();
            $nomeParametro = $parametro->getName();

            // Injeta o Request se for tipado como App\Auxilios\Request
            if ($tipo && !$tipo->isBuiltin() && $tipo->getName() === Request::class) {
                $argumentos[] = $request;
            } 
            // Injeta variáveis capturadas da rota (ex: {id})
            elseif (array_key_exists($nomeParametro, $parametrosRota)) {
                $argumentos[] = $parametrosRota[$nomeParametro];
            } 
            // Usa o valor padrão do parâmetro se existir na assinatura do método
            elseif ($parametro->isDefaultValueAvailable()) {
                $argumentos[] = $parametro->getDefaultValue();
            } 
            // Fallback caso não encontre correspondência
            else {
                $argumentos[] = null;
            }
        }

        return $argumentos;
    }
}