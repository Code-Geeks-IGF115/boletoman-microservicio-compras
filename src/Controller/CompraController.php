<?php

namespace App\Controller;

use App\Entity\Compra;
use App\Entity\DetalleCompra;
use App\Form\CompraType;
use App\Repository\CompraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\DetalleCompraRepository;
use App\Service\ResponseHelper;
use Nelmio\CorsBundle;

#[Route('/compra')]
class CompraController extends AbstractController
{
    private ResponseHelper $responseHelper;


    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    #[Route('/', name: 'app_compra_index', methods: ['GET'])]
    public function index(CompraRepository $compraRepository): Response
    {
        return $this->render('compra/index.html.twig', [
            'compras' => $compraRepository->findAll(),
        ]);
    }

    #[Route('/{idCategoria}/{idEvento}/new', name: 'app_compra_new', methods: ['POST'])]
    public function new(Request $request, CompraRepository $compraRepository, 
    DetalleCompraRepository $detalleCompraRepository,$idCategoria,$idEvento): JsonResponse
    {

        $parametros = $request->toArray();
        $request->request->replace(["compra"=>$parametros]);
        $compra = new Compra();
        $form = $this->createForm(CompraType::class, $compra);
        $form->handleRequest($request);

        //dd($parametros);                 
        $parametrosarray = $parametros['detalleCompra'];
        if (/*$form->isSubmitted() && */ $form->isValid()) {
            
            $compraRepository->save($compra, true);    //quitar barras despues
            foreach ($parametrosarray as $detalleComprasss) {

                $detalleComprasss['compra']=$compra;
                $detalleCompra = new DetalleCompra($detalleComprasss);
                //$detalleCompra->setDescripcion(strval("Butacas Categoria: " . $idCategoria . "     Evento: " . $idEvento));
                //$compra->addDetalleCompra($detalleCompra);
                $detalleCompraRepository->save($detalleCompra, true); //quitar barras despues
                //dd($detalleComprasss);      //poner barras despues
            } //consultar microservicio reservacion para poner datos de descripcion
            return $this->responseHelper->responseDatos(["message"=>"La compra ha sido guardada correctamente."]);
                    }
        else{
            return $this->responseHelper->responseMessage($form->getErrors());     
        } 
        //$parametros=$request->request->all(); 
        //$form = $this->createForm(DetalleCompraType::class, $detalleCompra);//sin formulario, usar repositorio de edtalle compra para pasar datos
            //$form->handleRequest($request);//Tomar array por separado            
            //$detalleCompra = new DetalleCompra();
            //$detalleCompra = $detalleCompraRepository->findBy(['compra' => $compra]);
            //$detalleCompra = $compra->getDetalleCompras();
            /*if($request->getContent())
            {
                $parametrosarray = json_decode($request->getContent(), true);
                
            }*/
            //guardar en base de datos con foreach
            
            /*return $this->redirectToRoute('app_compra_index', [], Response::HTTP_SEE_OTHER);*/

        //return $this->responseHelper->responseDatos($form->getErrors(true));
        /*return $this->renderForm('compra/new.html.twig', [
            'compra' => $compra,
            'form' => $form,
        ]);*/
    }

    #[Route('/{id}', name: 'app_compra_show', methods: ['GET'])]
    public function show(Compra $compra): Response
    {
        return $this->render('compra/show.html.twig', [
            'compra' => $compra,
        ]);
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
