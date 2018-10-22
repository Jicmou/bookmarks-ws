<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class JSONResponse
{

  private $response;

  public function __construct()
  {
    $this->response = new Response();
  }

  public function getJSONResponse($body, $status)
  {
    $this->response->setContent($body);
    $this->response->setStatusCode($status);
    $this->response->headers->set('Content-Type', 'application/json');
    return $this->response;
  }

  public function getNoContentResponse()
  {
    $this->response->setStatusCode(204);
    $this->response->headers->set('Content-Type', 'application/json');
    return $this->response;
  }

  public function getSuccessResponse(string $body)
  {
    return $this->getJSONResponse($body, 200);
  }

  public function getInternalErrorResponse(\Exception $e)
  {
    $statusCode = 500;
    $errorBody = json_encode(array(
      'code' => $statusCode,
      'message' => $e->getMessage()
    ));
    return $this->getJSONResponse($errorBody, $statusCode);
  }

  public function getRequestErrorResponse(string $message)
  {
    $statusCode = 400;
    $errorBody = json_encode(array(
      'code' => $statusCode,
      'message' => $message
    ));
    return $this->getJSONResponse($errorBody, $statusCode);
  }

  public function getNotFoundErrorResponse()
  {
    $statusCode = 404;
    $errorBody = json_encode(array(
      'code' => $statusCode,
      'message' => 'Ressource not found'
    ));
    return $this->getJSONResponse($errorBody, $statusCode);
  }
}
