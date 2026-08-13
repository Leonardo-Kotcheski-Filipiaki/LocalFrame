<?php

namespace App\Auxilios\Notifications;

use App\Auxilios\Essentials\Render;

class Toasts
{
    /**
     * Summary of __construct
     * @param string $mensagem (O que vai ser exibido)
     * @param string $tipo {'s' => "success", 'a' => 'warning', 'i' => 'primary'}
     */
    public function __construct(
        public string $mensagem,
        public string $tipo = 'i'
    ) {
        if (strlen($this->tipo) > 1 || strlen($this->tipo) < 1) {
            Render::erro('Tipo não pode ser vazio ou maior que 1 caractere', 500);
        }
    }

    public function getTipo() : string 
    {
        return match ($this->tipo) {
            's' => 'success',
            'a' => 'warning',
            'i' => 'primary'
        };
    }
}