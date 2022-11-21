<?php

namespace App\Controller;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\DetalleCompra;
use App\Form\DetalleCompraType;
use App\Repository\DetalleCompraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

#[Route('/detalle/compra')]
class DetalleCompraController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private $client;

    public function __construct(ResponseHelper $responseHelper, HttpClientInterface $client)
    {
        $this->responseHelper=$responseHelper;
        $this->client = $client;
    }

    #[Route('/', name: 'app_detalle_compra_index', methods: ['GET'])]
    public function index(DetalleCompraRepository $detalleCompraRepository): Response
    {
        return $this->render('detalle_compra/index.html.twig', [
            'detalle_compras' => $detalleCompraRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_detalle_compra_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DetalleCompraRepository $detalleCompraRepository): Response
    {
        $detalleCompra = new DetalleCompra();
        $form = $this->createForm(DetalleCompraType::class, $detalleCompra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detalleCompraRepository->save($detalleCompra, true);
            
            return $this->redirectToRoute('app_detalle_compra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detalle_compra/new.html.twig', [
            'detalle_compra' => $detalleCompra,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detalle_compra_show', methods: ['GET'])]
    public function show(DetalleCompra $detalleCompra = NULL): JsonResponse
    {
        if(!$detalleCompra){
            return $this->responseHelper->responseMessage("No existe dicha compra");
        }else{
            return $this->responseHelper->responseDatos(['detalleCompra' => $detalleCompra],['ver_detallecompra']);
        }
        
    }

    #[Route('/{id}/edit', name: 'app_detalle_compra_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DetalleCompra $detalleCompra, DetalleCompraRepository $detalleCompraRepository): Response
    {
        $form = $this->createForm(DetalleCompraType::class, $detalleCompra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detalleCompraRepository->save($detalleCompra, true);

            return $this->redirectToRoute('app_detalle_compra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detalle_compra/edit.html.twig', [
            'detalle_compra' => $detalleCompra,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_detalle_compra_delete', methods: ['POST'])]
    public function delete(Request $request, DetalleCompra $detalleCompra, DetalleCompraRepository $detalleCompraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$detalleCompra->getId(), $request->request->get('_token'))) {
            $detalleCompraRepository->remove($detalleCompra, true);
        }

        return $this->redirectToRoute('app_detalle_compra_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/imprimirasistentes/{idEvento}', name: 'app_detalle_compra_imprimir', methods: ['GET'])]
    public function imprimirAsistentes(Request $request, $idEvento): JsonResponse
    {
        $mensaje="Hola, bienvenido";
        try 
        {
            //$estado="Bloqueado";
            //$parametros = $request->toArray();
            //$butacasIDs=$parametros["butacas"];
            //Pt1
            $response = $this->client->request(
                'POST',
                'https://boletoman-reservaciones.herokuapp.com/disponibilidad/butacas/de/evento/'. $idEvento .'/pdf'
                        //[ /*'json' => ['' =>],*/   ]);
                //['disponibilidad'=>$disponibilidadDeButaca]
            );
                $resultadosDeConsulta=$response->toArray();
                foreach($resultadosDeConsulta as $resultadxs)
                {
                    $codigo=$resultadxs["codigoButaca"];
                    $categoriaButaca=$resultadxs["nombre"];
                    $idDetalleCompra=$resultadxs["idDetalleCompra"];
                }
                $datos=[
                    $codigo,$categoriaButaca,$idDetalleCompra
                ];
                return $this->responseHelper->responseDatos($datos);

            //Retornar codigo butaca, categoria butaca y el id detallecompra
        } 
        catch (Exception $e) 
        {
            return $this->responseHelper->responseDatosNoValidos($e->getMessage());
        }    
        return $this->responseHelper->responseDatos($mensaje);
    }
}
