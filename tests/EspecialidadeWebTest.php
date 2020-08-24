<?php

namespace App\tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadeWebTest extends WebTestCase{

    public function testGaranteQueRequisicaoFalhaSemAutenticacao()
    {
        $client = static::createClient();
        $client->request('GET', '/especialidades');

        self::assertEquals(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testGaranteQueEspecialidadesSaoListadas()
    {
        $client = static::createClient();
        
        $token = $this->login($client);

        $client->request(
            'GET', '/especialidades', [], [], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        $resposta = json_decode($client->getResponse()->getContent());
        self::assertTrue($resposta->sucesso);

    }

    public function testInsereEspecialidade(){
        $cliente = static::createClient();
        $token = $this->login($cliente);

        $cliente->request('POST', '/especialidades', [],[],[
                'CONTENT_TYPE' => 'aplication/json',
                'HTTP_AUTHORIZATION' => "Bearer $token"
        ], json_encode([
            'descricao' => 'Teste'
            ]
    ));
        self::assertEquals(200, $cliente->getResponse()->getStatusCode());

    }

    private function login( $client)
    {
        $client->request('POST', '/login',
            [], [],[
            'CONTENT_TYPE' => 'application/json'
        ],
            json_encode([
                'usuario' => 'usuario',
                'senha' => '123456'
        ]));

        return json_decode($client->getResponse()->getContent())
            ->access_token;
    }
}