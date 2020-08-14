<?php

namespace App\Controller;

use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse,
    Response};
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\MedicoFactory;

class MedicosController extends BaseControlller
{
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $factory,
        MedicoRepository $repository
    ) {

        parent::__construct($repository, $entityManager, $factory);

    }
    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function buscaPorEspecialidade($especialidadeId): Response{

            $medicos = $this->repository->findBy([
                'especialidade' => $especialidadeId
            ]);

            return new JsonResponse($medicos);

    }

    /**
     * @param Medico $entidadeExistente
     * @param Medico $entidadeEnviada
     */

    public function atualizaEntidadeExistente(
        $entidadeExistente, $entidadeEnviada
    )
    {
        $entidadeExistente->setCrm($entidadeEnviada->getCrm())
        ->setNome($entidadeEnviada->getNome)
        ->setEspecialidade($entidadeEnviada->getEspecialidade());
    }
}