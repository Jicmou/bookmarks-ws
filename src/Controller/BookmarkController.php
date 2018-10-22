<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Bookmark;
use App\Service\BookmarkDataRetriever;
use App\Service\BookmarkService;
use App\Service\JSONResponse;

class BookmarkController extends AbstractController
{

  private $jsonResponse;

  private $bookmarkDataRetriever;

  private $bookmarkService;

  private $logger;

  private $validUrl = '/^(http(s)?:\/\/(www.)?(vimeo|flickr).com\/)/';

  public function __construct(
    JSONResponse $jsonResponse,
    BookmarkDataRetriever $bookmarkDataRetriever,
    BookmarkService $bookmarkService,
    LoggerInterface $logger
  ) {
    $this->jsonResponse = $jsonResponse;
    $this->bookmarkDataRetriever = $bookmarkDataRetriever;
    $this->bookmarkService = $bookmarkService;
    $this->logger = $logger;

  }

  private function getUrlFromRequest(Request $request)
  {
    return json_decode($request->getContent())->url;
  }

  private function isUrlValid(string $url)
  {
    return preg_match($this->validUrl, $url);
  }

  private function isCreateRequestValid(Request $request)
  {
    return $this->isUrlValid($this->getUrlFromRequest($request));
  }

  private function createBookmark($args)
  {
    $bookmark = new Bookmark();
    $bookmark->create($args);
    return $bookmark;
  }

  private function getBookmarkPropertiesList(array $bookmarkList)
  {
    return array_map(function (Bookmark $bookmark) {
      return $bookmark->getProperties();
    }, $bookmarkList);
  }

  public function getList()
  {
    try {
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmarkList' => $this->getBookmarkPropertiesList(
              $this->bookmarkService->getBookmarkList()
            )
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

      if (!$this->isCreateRequestValid($request)) {
        return $this->getRequestErrorResponse('invalid url');
      }
      $bookmarkData = $this->bookmarkDataRetriever->retrieveBookmarkDataFromUrl(
        $this->getUrlFromRequest($request)
      );
      $newBookmark = $this->bookmarkService->saveBookmark(
        $this->createBookmark($bookmarkData)
      );
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmark' => $newBookmark->getProperties()
          )
        )
      );

    } catch (Exception $e) {
      $logger->error('Error in BookmarkController::create: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }

  public function getById($bookmarkId)
  {
    try {
      $bookmark = $this->bookmarkService->getBookmarkById(
        $bookmarkId
      );
      if (!$bookmark) {
        return $this->jsonResponse->getNotFoundErrorResponse();
      }
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmark' => $bookmark->getProperties()
          )
        )
      );

    } catch (Exception $e) {
      $logger->error('Error in BookmarkController::delete: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }

  public function delete($bookmarkId)
  {
    try {
      $bookmark = $this->bookmarkService->getBookmarkById(
        $bookmarkId
      );
      if (!$bookmark) {
        return $this->jsonResponse->getNotFoundErrorResponse();
      }
      $this->bookmarkService->deleteBookmark(
        $bookmark
      );
      return $this->jsonResponse->getNoContentResponse();

    } catch (Exception $e) {
      $logger->error('Error in BookmarkController::delete: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }
}
