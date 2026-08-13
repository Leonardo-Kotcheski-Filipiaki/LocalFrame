<?php

namespace Core\Essentials;

use Core\Notifications\Erros;
use Core\Notifications\Toasts;

class Render
{
    private static array $erros = [];
    private static array $toasts = [];

    public static function render(string $arquivo, array $dados = [])
    {
        $caminho = __DIR__ . "/../../App/Views/" . $arquivo . ".phtml";

        if (file_exists($caminho)) {
            $conteudo = file_get_contents($caminho) . file_get_contents(__DIR__ . '/../../App/Views/ui/toast.phtml');

            $conteudo = preg_replace_callback('/@include_layout\s*\([\'"](.*?)[\'"]\);?/', function($matches) {
                $arquivoInclude = __DIR__ . "/../../App/Views/_base/" . $matches[1] . ".phtml";
                return file_exists($arquivoInclude) ? file_get_contents($arquivoInclude) : "<!-- Include não encontrado: {$matches[1]} -->";
            }, $conteudo);

            $conteudo = preg_replace('/@if\s*\((.*)\)/', '<?php if($1): ?>', $conteudo);
            $conteudo = preg_replace('/@elseif\s*\((.*)\)/', '<?php elseif($1): ?>', $conteudo);
            $conteudo = preg_replace('/@else/', '<?php else: ?>', $conteudo);
            $conteudo = preg_replace('/@endif/', '<?php endif; ?>', $conteudo);
            $conteudo = preg_replace('/@foreach\s*\((.*)\)/', '<?php foreach($1): ?>', $conteudo);
            $conteudo = preg_replace('/@endforeach/', '<?php endforeach; ?>', $conteudo);
            $conteudo = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= $1 ?>', $conteudo);

            $toastsSessao = [];
            if (!empty($_SESSION['_toasts']) && is_array($_SESSION['_toasts'])) {
                foreach ($_SESSION['_toasts'] as $t) {
                    if ($t instanceof Toasts) {
                        $toastsSessao[] = $t;
                    } elseif (is_array($t)) {
                        $toastsSessao[] = new Toasts($t['mensagem'] ?? '', $t['tipo'] ?? 'info');
                    }
                }
            }

            $errosSessao = [];
            if (!empty($_SESSION['_erros']) && is_array($_SESSION['_erros'])) {
                foreach ($_SESSION['_erros'] as $e) {
                    if ($e instanceof Erros) {
                        $errosSessao[] = $e;
                    } elseif (is_array($e)) {
                        $errosSessao[] = new Erros($e['mensagem'] ?? '', $e['codigo'] ?? 400);
                    }
                }
            }

            $dados['erros'] = array_merge(self::$erros, $errosSessao);
            $dados['toasts'] = array_merge(self::$toasts, $toastsSessao);

            $dados['erros'] = empty($dados['erros']) ? null : $dados['erros'];
            $dados['toasts'] = empty($dados['toasts']) ? null : $dados['toasts'];
            
            unset($_SESSION['_toasts'], $_SESSION['_erros']);

            extract($dados);
            
            eval('?>' . $conteudo);
        } else {
            self::erro("View não encontrada", 500);
        }
    }

    public static function erro(string $mensagem, int $codigo = 400): void
    {
        self::render("erros/index", ["mensagem" => $mensagem, "codigo" => $codigo]);
        exit;
    }

    public static function toast(string $mensagem, string $tipo): void
    {
        self::$toasts[] = new Toasts($mensagem, $tipo);

        $_SESSION['_toasts'][] = [
            'mensagem' => $mensagem,
            'tipo' => $tipo
        ];
    }

    public static function hasToasts(): bool
    {
        return count(self::$toasts) > 0 || !empty($_SESSION['_toasts']);
    }
}