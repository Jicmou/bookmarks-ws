<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Bookmark;
use App\Repository\BookmarkRepository;
use App\Service\BookmarkDataRetriever;
use App\Service\JSONResponse;

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

  private function getBookmarkListFromORM(BookmarkRepository $bookmarkRepository)
  {
    return $bookmarkRepository->findAll();
  }

  private function getBookmarkByIdFromORM(BookmarkRepository $bookmarkRepository, $bookmarkId)
  {
    return $bookmarkRepository->find($bookmarkId);
  }

  private function saveBookmarkWithORM(EntityManagerInterface $entityManager, Bookmark $bookmark)
  {
    $entityManager->persist($bookmark);
    $entityManager->flush();
    return $bookmark;
  }

  private function deleteBookmarkWithORM(EntityManagerInterface $entityManager, Bookmark $bookmark)
  {
    $entityManager->remove($bookmark);
    $entityManager->flush();
    return $bookmark;
  }

  private function getBookmarkRepository()
  {
    return $this->getDoctrine()
      ->getRepository(Bookmark::class);
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
    $bookmark = new Bookmark;
    $bookmark->create($args);
    return $bookmark;
  }

  public function getList()
  {
    try {
      return $this->jsonResponse->getSuccessResponse(
        json_encode(
          array(
            'bookmarkList' => array_map(function (Bookmark $bookmark) {
              return $bookmark->getProperties();
            }, $this->getBookmarkListFromORM(
              $this->getBookmarkRepository()
            ))
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
        return $this->jsonResponse->getRequestErrorResponse('invalid url');
      }
      $bookmarkData = $this->bookmarkDataRetriever->retrieveBookmarkDataFromUrl(
        $this->getUrlFromRequest($request)
      );
      $newBookmark = $this->saveBookmarkWithORM(
        $this->getDoctrine()->getManager(),
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
      $bookmark = $this->getBookmarkByIdFromORM(
        $this->getBookmarkRepository(),
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
      $bookmark = $this->getBookmarkByIdFromORM(
        $this->getBookmarkRepository(),
        $bookmarkId
      );
      if (!$bookmark) {
        return $this->jsonResponse->getNotFoundErrorResponse();
      }
      $this->deleteBookmarkWithORM(
        $this->getDoctrine()->getManager(),
        $bookmark
      );
      return $this->jsonResponse->getNoContentResponse();

    } catch (Exception $e) {
      $logger->error('Error in BookmarkController::delete: ' . $e->getMessage());
      return $this->jsonResponse->getInternalErrorResponse($e);
    }
  }
}
