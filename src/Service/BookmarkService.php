<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Bookmark;

class BookmarkService extends AbstractController
{
  public function getBookmarkList()
  {
    return $this->getDoctrine()->getRepository(Bookmark::class)->findAll();
  }

  public function getBookmarkById($bookmarkId)
  {
    return $this->getDoctrine()->getRepository(Bookmark::class)->find($bookmarkId);
  }

  public function saveBookmark(Bookmark $bookmark)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($bookmark);
    $entityManager->flush();
    return $bookmark;
  }

  public function deleteBookmark(Bookmark $bookmark)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($bookmark);
    $entityManager->flush();
    return $bookmark;
  }

}