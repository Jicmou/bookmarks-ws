<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Tag;
use App\Service\JSONResponse;
use App\Service\TagService;

class TagController extends AbstractController
{
  private $jsonResponse;

  private $tagService;

  private $logger;

  public function __construct(
    JSONResponse $jsonResponse,
    LoggerInterface $logger,
    TagService $tagService
  ) {
    $this->jsonResponse = $jsonResponse;
    $this->logger = $logger;
    $this->tagService = $tagService;
  }

  private function getTagPropertiesList(array $tagList)
  {
    return array_map(function (Tag $tag) {
      return $tag->getProperties();
    }, $tagList);
  }

  public function getList()
  {
    try {
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'tagList' => $this->getTagPropertiesList(
              $this->tagService->getTagList()
            )
          )
        )
      );
    } catch (Exception $e) {
      $this->logger->error('Error in TagController::getList: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }

  public function getById($tagId)
  {
    try {
      $tag = $this->tagService->getTagById(
        $tagId
      );
      if (!$tag) {
        return $this->jsonResponse->getNotFoundErrorResponse();
      }
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'tag' => $tag->getProperties()
          )
        )
      );

    } catch (Exception $e) {
      $this->logger->error('Error in TagController::getById: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }
}
