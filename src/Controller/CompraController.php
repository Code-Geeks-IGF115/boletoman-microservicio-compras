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
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Nelmio\CorsBundle;

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
