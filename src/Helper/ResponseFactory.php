<?php
namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory{

    private bool $sucesso;
    private $conteudoResposta;
    private int $statusDaResposta;
    private $paginaAtual;
    private $itensPorPagina;
    
    public function __construct(
        bool $sucesso,
        $conteudoResposta,
        int $statusDaResposta = Response::HTTP_OK,
        $paginaAtual = null,
        int $itensPorPagina = null
        
    )
    {
     $this->sucesso = $sucesso;
     $this->ConteudoResposta = $conteudoResposta;
     $this->statusDaResposta = $statusDaResposta;
     $this->paginaAtual = $paginaAtual;
     $this->itensPorPagina = $itensPorPagina;
    }

    public function getResponse(): JsonResponse{

        $conteudoResposta = [
            'sucesso' => $this->sucesso,
            'paginaAtual' => $this->paginaAtual,
            'itensPorPagina' => $this->itensPorPagina,
            'conteudoResposta' => $this->conteudoResposta
        ];

        if(is_null($conteudoResposta['paginaAtual'])){
            unset($conteudoResposta['paginaAtual']);
            unset($conteudoResposta['itensPorPagina']);
        }

        return new JsonResponse($conteudoResposta, $this->statusDaResposta);
    }
}