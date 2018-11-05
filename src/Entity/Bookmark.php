<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookmarkRepository")
 */
class Bookmark
{

  /**
   * @ORM\Column(type="datetime")
   */
  private $creationDate;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $authorName;

  /**
   * @ORM\Column(type="integer", nullable=true)
   */
  private $duration;

  /**
   * @ORM\Column(type="integer")
   */
  private $height;

  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $title;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $type;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $url;

  /**
   * @ORM\Column(type="integer")
   */
  private $width;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="bookmarks")
   */
  private $tags;

  public function __construct()
  {
    $this->tags = new ArrayCollection();
  }

  public function create(array $args)
  {
    $this->authorName = $args['authorName'];
    $this->duration = $args['duration'];
    $this->height = $args['height'];
    $this->title = $args['title'];
    $this->type = $args['type'];
    $this->url = $args['url'];
    $this->width = $args['width'];
    $this->creationDate = new \DateTime('now');
  }

  public function getId() : ? int
  {
    return $this->id;

  }

  public function getProperties()
  {
    return array(
      'authorName' => $this->authorName,
      'creationDate' => $this->creationDate->format('c'),
      'duration' => $this->duration,
      'height' => $this->height,
      'id' => $this->id,
      'tags' => $this->getEndpointListFromTags(),
      'title' => $this->title,
      'type' => $this->type,
      'url' => $this->url,
      'width' => $this->width,
    );
  }

  public function getEndpointListFromTags()
  {
    return array_map(function (Tag $tag) {
      return $tag->getEndpointFromTag();
    }, $this->getTagsInArray());
  }

  /**
   * @return Collection|Tag[]
   */
  public function getTags() : Collection
  {
    return $this->tags;
  }

  public function addTag(Tag $tag) : self
  {
    if (!$this->tags->contains($tag)) {
      $this->tags[] = $tag;
      $tag->addBookmark($this);
    }

    return $this;
  }

  public function getTagsInArray()
  {
    return $this->getTags()->toArray();
  }

  public function removeTag(Tag $tag) : self
  {
    if ($this->tags->contains($tag)) {
      $this->tags->removeElement($tag);
      $tag->removeBookmark($this);
    }

    return $this;
  }


  public function setTagList(array $tagList)
  {
    $oldTagList = $this->getTagsInArray();
    array_map(function ($tag) {
      return $this->removeTag($tag);
    }, $oldTagList);
    array_map(function ($tag) {
      return $this->addTag($tag);
    }, $tagList);
  }

}
