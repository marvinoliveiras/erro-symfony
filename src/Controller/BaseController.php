<?php

namespace App\Controller;

use App\Helper\EntidadeFactoryInterface;
use App\Helper\ExtratorDadosRequest;
use App\Helper\ResponseFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntidadeFactoryInterface
     */
    protected $factory;
    /**
     * @var ExtratorDadosRequest
     */
    private $extratorDadosRequest;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectRepository $repository,
        EntidadeFactoryInterface $factory,
        ExtratorDadosRequest $extratorDadosRequest
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return new JsonResponse($entidade);
    }

    public function buscarTodos(Request $request)
    {
        $filtro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        $informacoesDeOrdenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        [$paginaAtual, $itensPorPagina] = $this->extratorDadosRequest->buscaDadosPaginacao($request);

        $lista = $this->repository->findBy(
            $filtro,
            $informacoesDeOrdenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );
        $fabricaResposta = new ResponseFactory(
            true,
            $lista,
            Response::HTTP_OK,
            $paginaAtual,
            $itensPorPagina
        );
        return $fabricaResposta->getResponse();
    }

    public function buscarUm(int $id): Response
    {
        $entidade = $this->repository->find($id);
        $statusResposta = is_null($entidade)
            ? Response::HTTP_NO_CONTENT
            : Response::HTTP_OK;
        $fabricaResposta = new ResponseFactory(
            true,
            $entidade,
            $statusResposta
        );

        return $fabricaResposta->getResponse();
    }

    public function remove(int $id): Response
    {
        $entidade = $this->repository->find($id);
        $this->entityManager->remove($entidade);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        try {
            $entidadeExistente = $this->atualizaEntidadeExistente($id, $entidade);
            $this->entityManager->flush();

            $fabrica = new ResponseFactory(
                true,
                $entidadeExistente,
                Response::HTTP_OK
            );
            return $fabrica->getResponse();
        } catch (\InvalidArgumentException $ex) {
            $fabrica = new ResponseFactory(
                false,
                'Recurso não encontrado',
                Response::HTTP_NOT_FOUND
            );
            return $fabrica->getResponse();
        }
    }

    abstract function atualizaEntidadeExistente(int $id, $entidade);
}
