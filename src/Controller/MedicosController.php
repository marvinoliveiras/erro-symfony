<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\ExtratorDadosRequest;
use App\Helper\MedicoFactory;
use App\Repository\MedicosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends BaseController
{
    /**
     * @var MedicoFactory
     */
    private $medicoFactory;
    /**
     * @var MedicosRepository
     */
    private $medicosRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory,
        MedicosRepository $medicosRepository,
        ExtratorDadosRequest $extratorDadosRequest
    ) {
        parent::__construct($entityManager, $medicosRepository, $medicoFactory, $extratorDadosRequest);
        $this->medicoFactory = $medicoFactory;
        $this->medicosRepository = $medicosRepository;
    }

    public function atualizaEntidadeExistente(int $id, $entidade)
    {
        /** @var Medico $entidadeExistente */
        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            throw new \InvalidArgumentException();
        }
        $entidadeExistente
            ->setCrm($entidade->getCrm())
            ->setNome($entidade->getNome());

        return $entidadeExistente;
    }

    /**
     * @Route("/especialidades/{especialidadeId}/medicos", methods={"GET"})
     */
    public function buscaPorEspecialidade(int $especialidadeId): Response
    {
        $medicos = $this->medicosRepository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }
}