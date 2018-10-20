<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Bookmark;

class BookmarkController extends AbstractController
{

  private function getJSONResponse($body, $status)
  {
    $response = new Response($body, $status);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  private function getSuccessResponse(string $body)
  {
    return $this->getJSONResponse($body, 200);
  }

  private function getInternalErrorResponse(Exception $e)
  {
    $statusCode = 500;
    $errorBody = json_encode(array(
      'code' => $statusCode,
      'message' => $e->getMessage()
    ));
    return $this->getJSONResponse($errorBody, $statusCode);
  }

  private function getBookmarkListFromORM()
  {
    return $this->getDoctrine()
      ->getRepository(Bookmark::class)
      ->findAll();
  }

  public function getList()
  {
    try {
      return $this->getSuccessResponse(
        json_encode(
          $this->getBookmarkListFromORM()
        )
      );
    } catch (Exception $e) {
      return $this->getInternalErrorResponse($e);
    }
  }
}
