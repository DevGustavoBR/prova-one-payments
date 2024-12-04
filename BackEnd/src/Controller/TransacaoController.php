<?php

namespace App\Controller;

use BackEnd\Src\Services\CartaoCredito;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use App\Entity\Transacao;
use App\Repository\TransacaoRepository;
use App\Services\GerarPix;

class TransacaoController extends AbstractController
{
    #[Route('/transacoes', name: 'transacoes', methods:['GET'])]
    public function transacoes(Request $request, TransacaoRepository $transacaoRepository): JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        if($this->verificaDadosCorpo($request) !== null) return $this->verificaDadosCorpo($request);


        $transacoes = $transacaoRepository->findAll();

        if(!$transacoes) return $this->json([
           'status' => 404,
           'message' => 'Nenhuma Transação Encontrado'
        ]);

        return $this->json([
            'status' => 200,
            'dados' => $transacoes
        ]);
    }

    #[Route('/transacao/{idTransacao}', name: 'transacao_id', methods:['GET'])]
    public function transacao(string $idTransacao,Request $request, TransacaoRepository $transacaoRepository): JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        if($this->verificaDadosCorpo($request) !== null) return $this->verificaDadosCorpo($request);
        if($this->verificaId($idTransacao) !== null) return $this->verificaId($idTransacao);

        $transacao = $transacaoRepository->findOneBy(['idTransacao' => $idTransacao]);

        if(!$transacao) return $this->json([
           'status' => 404,
           'message' => 'Transação Não Encontrada'
        ]);

        return $this->json([
            'status' => 200,
            'dados' => $transacao
        ]);
    }


    #[Route('/transacao/pagamento/criar/cartao-credito', name: 'transacao_criar_pagamento_cartao', methods:['POST'])]
    public function criarPagamentoCartao(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        
        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }

        $detalhes = [
          'cliente' => strip_tags($data['cliente']),
          'produto' => strip_tags($data['produto']),
          'valor'=> strip_tags($data['valor']),
          'servico' => strip_tags($data['servico']),
          'metodo_pagamento' => 'Cartão de Crédito',
        ];

        $transcao = uniqid();


        $transacao = new Transacao();
        $transacao->setIdTransacao($transcao);
        $transacao->setStatus('pedente');
        $transacao->setDetalhes($detalhes);
        $transacao->setCreatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));
        $transacao->setUpdatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));

        $entityManager->persist($transacao);

        try{
            $entityManager->flush();

            return $this->json([
                'status' => 201,
                'idTransacao' => $transcao,
                'message' => 'Link pagamento para cartão de crédito gerado',
                'dados' => $transacao
            ]);
        }catch(ORMException | DBALException $e){
            return $this->json([
                'status' => '500',
                'message' => 'Erro ao gerar link de pagamento para cartão de crédito',
                'error' => $e->getMessage()
               ]);
        }
    }


    #[Route('/transacao/processar/pagamento/cartao-credito/{idTransacao}', name: 'transacao_pagamento_cartao', methods:['PUT', 'PATCH'])]
    public function processarPagamentoCartao(string $idTransacao, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        if($this->verificaId($idTransacao) !== null) return $this->verificaId($idTransacao);

        $transacao = $entityManager->getRepository(Transacao::class)->findOneBy(['idTransacao' => $idTransacao]);

        if(!$transacao) return $this->json([
            'status' => 404,
            'message' => 'Transação Não Encontrada'
         ]);

        if($transacao->getStatus() === 'aprovada' OR $transacao->getStatus() === 'negada') return $this->json([
            'status' => 400,
             'message' => 'Pagamento já processado, atualmente se encontra com status:'.' '.$transacao->getStatus()  
        ]);

        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }


        $allowedKeys = ['cartao', 'validade', 'cvv'];
        $extraKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($extraKeys)) {
            return $this->json([
                'status' => 400,
                'message' => 'Dados inválidos, apenas os campos "cartao", "validade" e "cvv" são aceitos.'
            ]);
        }

        $cartaoCredito = new CartaoCredito;


        $numero = $data['cartao'];
        $validade = $data['validade'];
        $cvv = $data['cvv'];

        $erros = [];

        if(!$cartaoCredito->validarCartao($numero)){
           $erros['cartao'] = "Número do cartão inválido.";
        }

        if(!$cartaoCredito->validarValidade($validade)){
            $erros['validade'] = "Data de validade inválida.";
        }

        if(!$cartaoCredito->validarCVV($numero, $cvv)){
            $erros['cvv'] = "CVV inválido.";
        }

        if($erros) return $this->json([
          "status"=> 400,
          'message' => 'Cartão de Credito inválido, 
          aceitamos visa, mastercard e amex
          '
        ]);


        $status = rand(0, 1 ) ? 'aprovada' : 'negada' ;

        $transacao->setStatus($status);
        $transacao->setUpdatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));

        try{

        $entityManager->flush();
        
        return $this->json([
         'status' => 200,
         'message' => 'Pagamento foi Processado',
         'idTransacao' => $transacao->getIdTransacao(),
         'status_pagamento' => $transacao->getStatus(),
         'detalhes' => $transacao->getDetalhes()
        ]);

        }catch(ORMException | DBALException $e){
            return $this->json([
                'status' => 500,
                'message' => 'Erro ao processar seu pagamento Via Pix',
                'error' => $e->getMessage()
            ]);
        }
    }




    #[Route('/transacao/pagamento/criar/pix', name: 'transacao_criar_pix', methods:['POST'])]
    public function criarPix(Request $request, EntityManagerInterface $entityManager) : JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        

        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }

        $detalhes = [
          'cliente' => strip_tags($data['cliente']),
          'produto' => strip_tags($data['produto']),
          'valor'=> strip_tags($data['valor']),
          'servico' => strip_tags($data['servico']),
          'metodo_pagamento' => 'PIX',
          'chave_pix' => 'f8c9d949-5872-4af7-a0ae-0ff6db529251',          
          'descricao_pix' => 'Pagamento Via Pix', 
          'beneficiario_pix' => 'SISTEMAS GU LTDA', 
        ];

        $gerarPix = new GerarPix;

        $transcao = uniqid();

        $resultado = $gerarPix->gerarPix($detalhes);

        if(!$resultado) return $this->json([
            'status' => 500,
            'message' => 'Erro em Gerar Pix, por favor verifique',
        ]);

        $transacao = new Transacao();
        $transacao->setIdTransacao($transcao);
        $transacao->setStatus('pedente');
        $transacao->setDetalhes(array_merge($detalhes, $resultado));
        $transacao->setCreatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));
        $transacao->setUpdatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));

        

        $entityManager->persist($transacao);

        try{
            $entityManager->flush();

            return $this->json([
                'status' => 201,
                'idTransacao' => $transcao,
                'message' => 'Link de Pagamento Pix Gerado',
                'dados' => $transacao
            ]);
        }catch(ORMException | DBALException $e){
            return $this->json([
                'status' => 500,
                'message' => 'Erro ao gerar link de pagamento PIX',
                'error' => $e->getMessage()
               ]);
        }

    }


    

    #[Route('/transacao/processar/pagamento/pix/{idTransacao}', name: 'transacao_pagamento_pix', methods:['PUT', 'PATCH'])]
    public function processarPagamentoPix(string $idTransacao,Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        if($this->verificaDadosCorpo($request) !== null) return $this->verificaDadosCorpo($request);
        if($this->verificaId($idTransacao) !== null) return $this->verificaId($idTransacao);

        $transacao = $entityManager->getRepository(Transacao::class)->findOneBy(['idTransacao' => $idTransacao]);

        if(!$transacao) return $this->json([
            'status' => 404,
            'message' => 'Transação Não Encontrada'
         ]);

        if($transacao->getStatus() === 'aprovada' OR $transacao->getStatus() === 'negada') return $this->json([
            'status' => 400,
             'message' => 'Pagamento já processado, atualmente se encontra com status:'.' '.$transacao->getStatus()  
        ]);

        $status = rand(0, 1 ) ? 'aprovada' : 'negada' ;

        $transacao->setStatus($status);
        $transacao->setUpdatedAt(new \DateTimeImmutable("now", new \DateTimeZone("America/Sao_Paulo")));

        try{

        $entityManager->flush();
        
        return $this->json([
         'status' => 200,
         'messagem' => 'Pagamento foi Processado',
         'idTransacao' => $transacao->getIdTransacao(),
         'status_pagamento' => $transacao->getStatus(),
         'detalhes' => $transacao->getDetalhes()
        ]);

        }catch(ORMException | DBALException $e){
            return $this->json([
                'status' => 500,
                'message' => 'Erro ao processar seu pagamento Via Pix',
                'error' => $e->getMessage()
            ]);
        }
    }



    #[Route('/teste/cartao', name: 'teste_cartao', methods:['POST'])]
    public function testarCartao(Request $request) : JsonResponse
    {

        if($this->verificaQueryString($request) !== null) return $this->verificaQueryString($request);
        

        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }

        $cartaoCredito = new CartaoCredito;


        $numero = $data['cartao'];
        $validade = $data['validade'];
        $cvv = $data['cvv'];

        $erros = [];

        if(!$cartaoCredito->validarCartao($numero)){
           $erros['cartao'] = "Número do cartão inválido.";
        }

        if(!$cartaoCredito->validarValidade($validade)){
            $erros['validade'] = "Data de validade inválida.";
        }

        if(!$cartaoCredito->validarCVV($numero, $cvv)){
            $erros['cvv'] = "CVV inválido.";
        }

        if($erros) return $this->json([
          'status'=> 400,
          'message' => $erros
        ]);


        return $this->json([
           "status" => 200,
            'message' => 'Cartão Valido'            
        ]);

    }
    


    private function verificaQueryString(Request $request): ?JsonResponse
    {
       if(count($request->query->all()) > 0) return  $this->json([
           'status' => 400,
           'message' => 'Parâmetros adicionais na query string não são permitidos.',
       ]);

       return null;
    }

    private function verificaDadosCorpo(Request $request): ?JsonResponse
    {

        if (!empty($request->getContent())) return $this->json([
            'status' => 400,
            'message' => 'Dados no corpo da requisição não são permitidos.',
        ]);

        return null;
    }


    private function verificaId(string $id): ?JsonResponse
    {

        if ($id !== null && !is_string($id)) return $this->json([
            'status' => 400,
            'message' => 'O ID fornecido não é válido.',
        ]); 

        return null;
    }

}
