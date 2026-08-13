<?php

namespace Core\Essentials;

class Request
{
    protected string $metodo;
    protected string $uri;
    protected array $dados = [];
    protected array $headers = [];

    public function __construct()
    {
        $this->metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->headers = $this->capturarHeaders();
        $this->dados = $this->capturarDados();
    }

    /**
     * Captura todos os dados da requisição
     * @return array
     */
    private function capturarDados(): array
    {
        $dados = [];

        if (!empty($_GET)) {
            $dados = array_merge($dados, $_GET);
        }
        if (!empty($_POST)) {
            $dados = array_merge($dados, $_POST);
        }
        $corpoJson = json_decode(file_get_contents('php://input'), true);
        if (is_array($corpoJson)) {
            $dados = array_merge($dados, $corpoJson);
        }
        return $dados;
    }

    /**
     * Captura todos os headers da requisição
     * @return array
     */
    private function capturarHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $chave => $valor) {
            if (str_starts_with($chave, 'HTTP_')) {
                $nomeHeader = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($chave, 5)))));
                $headers[$nomeHeader] = $valor;
            }
        }
        return $headers;
    }

    /**
     * Retorna o método da requisição
     * @return string
     */
    public function getMetodo(): string
    {
        return $this->metodo;
    }

    /**
     * Retorna a URI da requisição
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Retorna todos os dados da requisição
     * @return array
     */
    public function all(): array
    {
        return $this->dados;
    }

    /**
     * Retorna um dado específico da requisição
     * @param string $chave
     * @param mixed $padrao
     * @return mixed
     */
    public function input(string $chave, mixed $padrao = null): mixed
    {
        return $this->dados[$chave] ?? $padrao;
    }

    /**
     * Verifica se um dado existe na requisição
     * @param string $chave
     * @return bool
     */
    public function has(string $chave): bool
    {
        return isset($this->dados[$chave]);
    }

    /**
     * Retorna um header específico da requisição
     * @param string $chave
     * @return ?string
     */
    public function getHeader(string $chave): ?string
    {
        return $this->headers[$chave] ?? null;
    }
}