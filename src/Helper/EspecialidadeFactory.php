<?php

namespace App\Helper;

use App\Entity\Especialidade;
use App\Helper\EntityFactoryException;

class EspecialidadeFactory implements EntidadeFactoryInterface
{
    public function criarEntidade(string $json)
    {
        $objetoJson = json_decode($json);

        if(!property_exists($objetoJson, 'descricao')){
            throw new EntityFactoryException('Para criar uma especialidade é necessário enviar a descrição!');
        }
        $especialidade = new Especialidade();
        $especialidade->setDescricao($objetoJson->descricao);

        return $especialidade;
    }
}
