<?php

namespace App\Helper;

interface EntidadeFactoryInterface
{
    public function criarEntidade(string $json);
}