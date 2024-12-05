<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProdutoRepository;
use App\Services\SqlInjection;

class ProdutoController extends AbstractController
{
    #[Route('/produtos', name: 'produtos_lista', methods: ['GET'])]
    public function index(ProdutoRepository $produtoRepository, Request $request): JsonResponse
    {

        $validacao = $this->validarRequest($request);
        if ($validacao !== null) return $validacao;


        $dados = $produtoRepository->findAll();

        if(count($dados) == 0) return $this->json([
           'status' => 404,
           'message' => 'Nenhum Produto Encontrado'
        ]);


        return $this->json([
            'status' => 200,
            'data' =>  $dados
        ]);
    }


    #[Route('/produtos/{id}', name: 'produtos_id', methods: ['GET'])]
    public function produto(int $id,Request $request, ProdutoRepository $produtoRepository): JsonResponse
    {

        $validacao = $this->validarRequest($request, $id);
        if ($validacao !== null) return $validacao;

        $input = new SqlInjection;

        $id = $input->sanitizaInput($id);
        $id = $input->sanitizaSqlInput($id);

        $produto = $produtoRepository->find($id);

        if(!$produto) return $this->json([
           'status' => 404,
           'message' => 'Produto Encontrado'
        ]);


        return $this->json([
            'status' => 200,
            'message' => 'Produto encontrado',
            'data' => $produto
        ]);
    }


    private function validarRequest(Request $request, ?int $id = null): ?JsonResponse
    {
        if (count($request->query->all()) > 0) return $this->json([
                'status' => 400,
                'message' => 'Parâmetros adicionais na query string não são permitidos.',
            ]);
        

        if (!empty($request->getContent())) return $this->json([
                'status' => 400,
                'message' => 'Dados no corpo da requisição não são permitidos.',
            ]);
        

        if ($id !== null && $id <= 0) return $this->json([
                'status' => 400,
                'message' => 'O ID fornecido não é válido.',
            ]);
        

        return null;
    }


}
