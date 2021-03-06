<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    JsonResponse,Request,Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \Firebase\JWT\JWT;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->encoder = $encoder;
    }
    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request)
    {
        
        $dadosEmJson = json_decode($request->getContent());

        if(is_null($dadosEmJson->usuario) || is_null($dadosEmJson->senha)){
            return new JsonResponse([
                'erro' => "Favor enviar usuário e senha"
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy([
            'username' => $dadosEmJson->usuario
            ]);

            if(!$this->encoder->isPasswordValid($user, $dadosEmJson->senha)){
                return new JsonResponse([
                    'erro' => 'Usuário e/ou senha inválido(s)'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = JWT::encode(['username' => $user->getUsername()], 'chave', 'HS256');

            return new JsonResponse([
                'access_token' => $token
            ]);

    }
}
