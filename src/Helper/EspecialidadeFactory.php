<?php

namespace App\Helper;

use App\Entity\Especialidade;

class EspecialidadeFactory implements EntidadeFactoryInterface
{
    public function criarEntidade(string $json)
    {
        $dadosEmJson = json_decode($json);
        $especialidade = new Especialidade();
        $especialidade->setDescricao($dadosEmJson->descricao);

        return $especialidade;
    }
}
