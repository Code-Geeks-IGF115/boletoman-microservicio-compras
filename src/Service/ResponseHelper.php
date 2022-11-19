<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\{JsonResponse};
use Symfony\Component\Serializer\SerializerInterface;

class ResponseHelper
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    public function responseDatos($data,$groups = null): JsonResponse
    {
        $response=new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '%env(resolve:CORS_ALLOW_ORIGIN)%');
        if($groups){
                $result = $this->serializer->serialize($data,'json',['groups' => $groups]);
        }else{

            $result = $this->serializer->serialize($data,'json');
        }
        return $response->fromJsonString($result);
    }

    public function responseDatosNoValidos($message=null): JsonResponse
    {
        if(!$message){
            $message="Datos no válidos.";
        }
        $response=new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '%env(resolve:CORS_ALLOW_ORIGIN)%');
        $result= $this->serializer->serialize(['message'=>$message],'json');
        $response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        return $response->fromJsonString($result);
    }

    public function responseMessage($message=null): JsonResponse
    {
        if(!$message){
            $message="Guardado.";
        }
        $response=new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '%env(resolve:CORS_ALLOW_ORIGIN)%');
        $result= $this->serializer->serialize(['message'=>$message],'json');
        return $response->fromJsonString($result);
    }
}