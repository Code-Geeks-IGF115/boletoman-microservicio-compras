<?php

namespace App\Controller;


use App\Entity\{Compra, DetalleCompra};
use App\Form\CompraType;
use App\Repository\CompraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\DetalleCompraRepository;
use App\Service\ResponseHelper;
use Exception;
use Nelmio\CorsBundle;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/compra')]
class CompraController extends AbstractController
{


    private ResponseHelper $responseHelper;
    private $client;

    public function __construct(ResponseHelper $responseHelper, HttpClientInterface $client)
    {
        $this->responseHelper=$responseHelper;
        $this->client = $client;
    }



    #[Route('/', name: 'app_compra_index', methods: ['GET'])]
    public function index(CompraRepository $compraRepository): Response
    {
        return $this->render('compra/index.html.twig', [
            'compras' => $compraRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_compra_new', methods: ['POST'])]
    public function new(Request $request, CompraRepository $compraRepository, 
    DetalleCompraRepository $detalleCompraRepository): JsonResponse
    {
        //$mensaje="//aca debe  ir que no sirve el coso y no hay boletos."
        try
        {            
            $parametros = $request->toArray();
            $request->request->replace(["compra"=>$parametros]);
            $compra = new Compra();
            $form = $this->createForm(CompraType::class, $compra);
            $form->handleRequest($request);
            $parametrosarray = $parametros['detalleCompra'];
            if ($form->isValid()) 
            {
                $parametros=$request->toArray(); 
                $idEvento=$parametros["idEvento"];
                $disponibilidades=$parametros["disponibilidades"];
                //De aca
                $response = $this->client->request(
                    'POST', 
                    'https://boletoman-reservaciones.herokuapp.com/disponibilidad/comprarbutacas', [                 // defining data using an array of parameters
                    'json' => ['idEvento' => $idEvento],['disponibilidades' => $disponibilidades]
                ]);
                //$statusCode=$response->getStatusCode();
                $resultadosDeConsulta=$response->toArray();
                //$content = $response->getContent(); dudando si usarlo
                //De alguna manera obtener el codigo htt
                $disponibilidad=$resultadosDeConsulta["disponibilidad"];
                $code=$disponibilidad->responseHelper->responseDatos(["status"]);
                //La funcion original
                if($code == 200)
                {
                    $compraRepository->save($compra, true);
                    foreach ($parametrosarray as $detalleComprasss) 
                    {
                        $detalleComprasss['compra']=$compra;
                        $detalleCompra = new DetalleCompra();
                        $detalleCompra->setDescripcion($detalleComprasss['descripcion']);
                        $detalleCompra->setCantidad($detalleComprasss['cantidad']);
                        $detalleCompra->setTotal($detalleComprasss['total']);
                        $detalleCompra->setCompra($detalleComprasss['compra']);
                        //dd($detalleComprasss);
                        $detalleCompraRepository->save($detalleCompra, true);
                    }
                    return $this->responseHelper->responseDatos(["message"=>"La compra ha sido guardada correctamente."]);
                }
                else if ($code == 412) {
                    //los boletos no están disponibles.
                    return $this->responseHelper->responseMessage("Los boletos no están disponibles. ".$response->getStatusCode());  
                }         
            
            }
            else{
                return $this->responseHelper->responseMessage($form->getErrors());     
            }
        }
        catch(Exception $ex)
        {
            return $this->responseHelper->responseDatosNoValidos($ex->getMessage());
        }
        //Aca no deberia llegar a este punto, ya que solo debe ser retornado 
        //codigo 200 o 412, ya veré que pongo.
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