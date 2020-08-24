<?php

namespace App\Controller;

use App\Helper\EntidadeFactoryInterface;
use App\Helper\ExtratorDadosRequest;
use App\Helper\ResponseFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\CacheItem;
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

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectRepository $repository,
        EntidadeFactoryInterface $factory,
        ExtratorDadosRequest $extratorDadosRequest,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem(
            $this->cachePrefix()
            .$entidade->getId());

            $cacheItem->set($entidade);
            $this->cache->save($cacheItem);

            $this->logger->notice("novo registro de {entidade} adicionado com id: {id}!",
                [
                    'entidade' => get_class($entidade),
                    'id' => $entidade->getId()
                ]
            );
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
        $entidade = $this->cache->hasItem($this->cachePrefix().$id)
            ? $this->cache->getItem($this->cachePrefix().$id)->get()
            : $this->repository->find($id);
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

        $this->cache->deleteItem(
            $this->cachePrefix()
            .$id);
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $entidade = $this->factory->criarEntidade($corpoRequisicao);

        try {
            $entidadeExistente = $this->atualizaEntidadeExistente($id, $entidade);
            $this->entityManager->flush();

            $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
            $cacheItem->set($entidadeExistente);
            $this->cache->save($cacheItem);


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
    
    abstract public function cachePrefix(): string;
}
