<?php

namespace App\Controller;

use App\Entity\Descuento;
use App\Form\DescuentoType;
use App\Repository\DescuentoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/descuento')]
class DescuentoController extends AbstractController
{
    #[Route('/', name: 'app_descuento_index', methods: ['GET'])]
    public function index(DescuentoRepository $descuentoRepository): Response
    {
        return $this->render('descuento/index.html.twig', [
            'descuentos' => $descuentoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_descuento_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DescuentoRepository $descuentoRepository): Response
    {
        $descuento = new Descuento();
        $form = $this->createForm(DescuentoType::class, $descuento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $descuentoRepository->save($descuento, true);

            return $this->redirectToRoute('app_descuento_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('descuento/new.html.twig', [
            'descuento' => $descuento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_descuento_show', methods: ['GET'])]
    public function show(Descuento $descuento): Response
    {
        return $this->render('descuento/show.html.twig', [
            'descuento' => $descuento,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_descuento_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Descuento $descuento, DescuentoRepository $descuentoRepository): Response
    {
        $form = $this->createForm(DescuentoType::class, $descuento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $descuentoRepository->save($descuento, true);

            return $this->redirectToRoute('app_descuento_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('descuento/edit.html.twig', [
            'descuento' => $descuento,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_descuento_delete', methods: ['POST'])]
    public function delete(Request $request, Descuento $descuento, DescuentoRepository $descuentoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$descuento->getId(), $request->request->get('_token'))) {
            $descuentoRepository->remove($descuento, true);
        }

        return $this->redirectToRoute('app_descuento_index', [], Response::HTTP_SEE_OTHER);
    }
}
