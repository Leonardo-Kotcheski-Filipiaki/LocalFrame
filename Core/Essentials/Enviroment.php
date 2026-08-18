<?php

namespace Core\Essentials;


//.env Reader
class Enviroment
{

    private bool $setted = false;

    public function __construct()
    {
        if ($this->setted) {
            return;
        }
        $this->setEnviroment();
    }

    private function setEnviroment()
    {
        $file = file_get_contents(__DIR__ . "/../../.env");
        $file = explode("\n", $file);
        foreach ($file as $line) {
            if (empty($line)) {
                continue;
            }
            if (strpos($line, "#") !== false) {
                continue;
            }
            $key = trim(explode("=", $line)[0]);
            $value = trim(explode("=", $line)[1]);
            if (strtolower($value) == "true") {
                $value = true;
            } else if (strtolower($value) == "false") {
                $value = false;
            }
            $this->{$key} = $value;
        }
    }

    public function get(string $key): ?string
    {
        if (!$this->setted) {
            $this->setEnviroment();
            $this->setted = true;
        }
        return $this->{$key} ?? null;
    }
}