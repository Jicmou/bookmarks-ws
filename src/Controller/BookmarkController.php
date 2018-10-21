<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Bookmark;
use App\Service\JSONResponse;

class BookmarkController extends AbstractController
{

  private $jsonResponse;

  public function __construct(JSONResponse $jsonResponse)
  {
    $this->jsonResponse = $jsonResponse;
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
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmarkList' => $this->getBookmarkListFromORM()
          )
        )
      );
    } catch (Exception $e) {
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }
}
