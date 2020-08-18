<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class ExtratorDadosRequest
{
    private function buscaDadosRequest(Request $request)
    {
        $queryString = $request->query->all();
        $dadosOrdenacao = array_key_exists('sort', $queryString)
            ? $queryString['sort']
            : null;
        unset($queryString['sort']);
        $paginaAtual = array_key_exists('page', $queryString)
        ? $queryString['page']: 1;
        unset($queryString['page']);

        $itensPorPagina = array_key_exists('itensPorPagina', $queryString)
        ? $queryString['page']: 5;
        unset($queryString['itensPorPagina']);


        return [$queryString, $dadosOrdenacao, $paginaAtual, $itensPorPagina];
    }

    public function buscaDadosOrdenacao(Request $request)
    {
        [, $ordenacao] = $this->buscaDadosRequest($request);
        return $ordenacao;
    }

    public function buscaDadosFiltro(Request $request)
    {
        [$filtro, ] = $this->buscaDadosRequest($request);
        return $filtro;
    }

    public function buscaDadosPaginacao(Request $request){
        [,,$paginaAtual, $itensPorPagina] = $this->buscaDadosRequest($request);

        return [$paginaAtual, $itensPorPagina];
    }
}
