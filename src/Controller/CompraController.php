<?php

namespace App\Controller;


use App\Entity\{Compra, DetalleCompra};
use App\Form\CompraType;
use App\Repository\{CompraRepository, DetalleCompraRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\ResponseHelper;
use Exception;

use Nelmio\CorsBundle;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/compra')]
class CompraController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private $client;


    public function __construct(ResponseHelper $responseHelper,HttpClientInterface $client)

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


    #[Route('/ejemplo/cliente', name: 'ejemplo_cliente', methods: ['POST'])]
    public function ejemploCliente(Request $request): JsonResponse
    {
        $mensaje="Hola Mundo!";
        
        try{
            // recibiendo parametros
            //SOY SERVIDOR
            $parametros=$request->toArray(); 
            $miNombre=$parametros["nombreCompleto"];
            // contruyendo cliente - AGREGACIÓN - TAMBIÉN SOY CLIENTE
            $response = $this->client->request(
                'POST', 
                'https://boletoman-reservaciones.herokuapp.com/sala/de/eventos/ejemplo/servidor', [
                // defining data using an array of parameters
                'json' => ['miNombre' => $miNombre],
            ]);
            $resultadosDeConsulta=$response->toArray();
            $mensaje=$resultadosDeConsulta["message"];
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($mensaje);  
        }

        return $this->responseHelper->responseMessage($mensaje);     
    }


   
    //recibiendo de microservicio reservaciones.
    #[Route('/mis/eventos/{idUsuario}', name: 'consulta_idUsuario', methods: ['GET'])]
    public function misEventos($idUsuario, DetalleCompraRepository $detalleCompraRepository)
    {
        $detalleCompras=$detalleCompraRepository->findByUsuario($idUsuario);
        $idsDetalleCompras=[];
        foreach ($detalleCompras as $key => $detalleCompra) {
            $idsDetalleCompras[]=$detalleCompra["id"];
        }
        //cliente consulta a microservicio reservaciones
        // dd(['idsDetallesCompra' => $idsDetalleCompras]);
        $response = $this->client->request(
            'GET', 
            'https://boletoman-reservaciones.herokuapp.com/disponibilidad/mis/eventos/', [
            // defining data using an array of parameters
            'json' => ['idsDetallesCompra' => $idsDetalleCompras],
            'timeout' => 90
            ]
        );
        $eventos=$response->toArray()["eventos"];
//         retornar eventos que pertenecen a disponibilidades que tienen los id detalles compra encontrados
        return $this->responseHelper->responseDatos(["eventos"=>$eventos]);
    }



    #[Route('/{idCompra}/boletos/pdf', name: 'boletos_cliente', methods: ['POST'])]
    public function boletos(DetalleCompraRepository $detalleCompraRepository,
    $idCompra): JsonResponse
    {
        $mensaje="Hola Mundo!";
        $compras = $detalleCompraRepository->findBy(['compra' => $idCompra]);
        //dd($compras);
        
        /*try{
            // recibiendo parametros
            //SOY SERVIDOR
            //$parametros=$request->toArray(); 
            //$miNombre=$parametros["nombreCompleto"];
            // contruyendo cliente - AGREGACIÓN - TAMBIÉN SOY CLIENTE
            $response = $this->client->request(
                'POST', 
                'https://boletoman-reservaciones.herokuapp.com/sala/de/eventos/ejemplo/servidor', [
                // defining data using an array of parameters
                'json' => ['miNombre' => $idCompra],
            ]);
            $resultadosDeConsulta=$response->toArray();
            $mensaje=$resultadosDeConsulta["message"];
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($mensaje);  
        }*/

        return $this->responseHelper->responseDatos($compras, ['ver_boletos']);     
    }

}

