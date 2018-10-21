<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookmarkRepository")
 */
class Bookmark
{

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $addedDate;

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

  public function create(array $args)
  {
    $this->addedDate = $args['addedDate'];
    $this->authorName = $args['authorName'];
    $this->duration = $args['duration'];
    $this->height = $args['height'];
    $this->title = $args['title'];
    $this->type = $args['type'];
    $this->url = $args['url'];
    $this->width = $args['width'];
  }

  public function getId() : ? int
  {
    return $this->id;

  }

  public function getProperties()
  {
    return array(
      'addedDate' => $this->addedDate,
      'authorName' => $this->authorName,
      'duration' => $this->duration,
      'height' => $this->height,
      'title' => $this->title,
      'type' => $this->type,
      'url' => $this->url,
      'width' => $this->width,
    );
  }

}
