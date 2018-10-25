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
use App\Service\TagService;

class BookmarkController extends AbstractController
{

  private $jsonResponse;

  private $bookmarkDataRetriever;

  private $bookmarkService;

  private $tagService;

  private $logger;

  private $validUrl = '/^(http(s)?:\/\/(www.)?((vimeo|flickr).com|flic.kr)\/)/';

  public function __construct(
    JSONResponse $jsonResponse,
    BookmarkDataRetriever $bookmarkDataRetriever,
    BookmarkService $bookmarkService,
    LoggerInterface $logger,
    TagService $tagService
  ) {
    $this->jsonResponse = $jsonResponse;
    $this->bookmarkDataRetriever = $bookmarkDataRetriever;
    $this->bookmarkService = $bookmarkService;
    $this->logger = $logger;
    $this->tagService = $tagService;

  }

  private function getUrlFromRequest(Request $request)
  {
    return json_decode($request->getContent())->url;
  }

  private function getTagNameListFromRequest(Request $request)
  {
    $tagNameList = json_decode($request->getContent())->tagNameList;
    return json_decode($request->getContent())->tagNameList;
  }

  private function getTagListFromTagNameList(array $tagNameList)
  {
    $tagList = $this->tagService->getTagListByName($tagNameList);
    return $tagList;
  }

  private function getTagListFromRequest(Request $request)
  {
    return $this->getTagListFromTagNameList(
      $this->getTagNameListFromRequest($request)
    );
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
      $this->logger->error('Error in BookmarkController::getList: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }

  public function create(Request $request)
  {
    try {

      if (!$this->isCreateRequestValid($request)) {
        return $this->jsonResponse->getRequestErrorResponse('invalid url');
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
      $this->logger->error('Error in BookmarkController::create: ' . $e->getMessage());
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
      $this->logger->error('Error in BookmarkController::getById: ' . $e->getMessage());
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
      $this->logger->error('Error in BookmarkController::delete: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }

  public function updateTagList(Request $request, $bookmarkId)
  {
    try {

      $bookmark = $this->bookmarkService->getBookmarkById(
        $bookmarkId
      );

      if (!$bookmark) {
        return $this->jsonResponse->getNotFoundErrorResponse();
      }

      $tagList = $this->getTagListFromRequest($request);
      $this->logger->info('tag List: ', $tagList);
      $bookmark->setTagList($tagList);

      $this->bookmarkService->saveBookmark($bookmark);
      array_map(function ($tag) {
        return $this->tagService->saveTag($tag);
      }, $tagList);

      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmark' => $bookmark->getProperties()
          )
        )
      );
    } catch (Exception $e) {
      $this->logger->error('Error in BookmarkController::updateTags: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }


}
