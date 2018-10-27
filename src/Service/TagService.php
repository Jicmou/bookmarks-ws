<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Tag;

class TagService extends AbstractController
{
  public function createTag(string $tagName)
  {
    $tag = new Tag();
    $tag->setName($tagName);
    $this->saveTag($tag);
    return $tag;
  }

  public function getTagList()
  {
    return $this->getDoctrine()->getRepository(Tag::class)->findAll();
  }

  public function getTagById($tagId)
  {
    return $this->getDoctrine()->getRepository(Tag::class)->find($tagId);
  }

  public function getTagByName(string $tagName)
  {
    $tag = $this->getDoctrine()->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
    if (!$tag) {
      $tag = $this->createTag($tagName);
    }
    return $tag;
  }

  public function getTagListByName($tagNameList)
  {
    return array_map(function ($tagName) {
      return $this->getTagByName($tagName);
    }, $tagNameList);
  }

  public function saveTag(Tag $tag)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($tag);
    $entityManager->flush();
    return $tag;
  }

}