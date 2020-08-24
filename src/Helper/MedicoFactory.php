<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntidadeFactoryInterface
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarEntidade(string $json): Medico
    {
        $objetoJson = json_decode($json);

        $this->checkAllProperties($objetoJson);
        
        $medico = new Medico();
        $medico
            ->setCrm($objetoJson->crm)
            ->setNome($objetoJson->nome)
            ->setEspecialidade(
                $this->especialidadeRepository
                ->find($objetoJson->especialidadeId));

        return $medico;
    }

    public function checkAllProperties(Object $objetoJson){
        if (!property_exists($objetoJson, 'nome')||
        !property_exists($objetoJson, 'crm')||
        !property_exists($objetoJson, 'especialidadeId')
        ){
            throw new EntityFactoryException('Médico necessita de Nome, CRM e Especialidade');
        }
    }
}