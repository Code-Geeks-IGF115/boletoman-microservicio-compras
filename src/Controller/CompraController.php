<?php

namespace App\Controller;

use App\Entity\{Compra};

use App\Form\CompraType;
use App\Repository\CompraRepository;
use App\Service\ResponseHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/compra')]
class CompraController extends AbstractController
{

    private ResponseHelper $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper=$responseHelper;
    }

    #[Route('/', name: 'app_compra_index', methods: ['GET'])]
    public function index(CompraRepository $compraRepository): Response
    {
        return $this->render('compra/index.html.twig', [
            'compras' => $compraRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_compra_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CompraRepository $compraRepository): Response
    {
        $compra = new Compra();
        $form = $this->createForm(CompraType::class, $compra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compraRepository->save($compra, true);

            return $this->redirectToRoute('app_compra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('compra/new.html.twig', [
            'compra' => $compra,
            'form' => $form,
        ]);
    }

   
    #[Route('/{id}', name: 'app_compra_show', methods: ['GET'])]
    public function show(Compra $compra = NULL): Response
    {
        if(!$compra){
            return $this->responseHelper->responseMessage("No existe dicha compra");
        }else{
            return $this->responseHelper->responseDatos(['Compra' => $compra],['ver_compra']);
        }
        
    }

    #[Route('/{id}/edit', name: 'app_compra_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compra $compra, CompraRepository $compraRepository): Response
    {
        $form = $this->createForm(CompraType::class, $compra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compraRepository->save($compra, true);

            return $this->redirectToRoute('app_compra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('compra/edit.html.twig', [
            'compra' => $compra,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compra_delete', methods: ['POST'])]
    public function delete(Request $request, Compra $compra, CompraRepository $compraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compra->getId(), $request->request->get('_token'))) {
            $compraRepository->remove($compra, true);
        }

        return $this->redirectToRoute('app_compra_index', [], Response::HTTP_SEE_OTHER);
    }
}
