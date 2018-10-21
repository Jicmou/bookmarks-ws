<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

use App\Entity\Bookmark;
use App\Service\JSONResponse;
use App\Service\BookmarkDataRetriever;

class BookmarkController extends AbstractController
{

  private $jsonResponse;
  private $bookmarkDataRetriever;
  private $logger;

  private $validUrl = '/^(http(s)?:\/\/(www.)?(vimeo|flickr).com\/)/';

  public function __construct(
    JSONResponse $jsonResponse,
    BookmarkDataRetriever $bookmarkDataRetriever,
    LoggerInterface $logger
  ) {
    $this->jsonResponse = $jsonResponse;
    $this->bookmarkDataRetriever = $bookmarkDataRetriever;
    $this->logger = $logger;
  }

  private function getBookmarkListFromORM()
  {
    return $this->getDoctrine()
      ->getRepository(Bookmark::class)
      ->findAll();
  }

  private function isUrlValid(string $url)
  {
    return preg_match($this->validUrl, $url);
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

  public function create(Request $request)
  {
    try {
      $url = json_decode($request->getContent())->url;
      if (!$this->isUrlValid($url)) {
        return $this->jsonResponse->getRequestErrorResponse('invalid url');
      }
      $this->logger->info('BookmarkController url: ' . json_encode($url));
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmark' => $this->bookmarkDataRetriever->getLinkData($url)
          )
        )
      );
    } catch (Exception $e) {
      $logger->error('Error in BookmarkController::create: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }

  }
}
