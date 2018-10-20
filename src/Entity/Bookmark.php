<?php

namespace App\Entity;

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
  private $url;

  public function create(array $args)
  {
    $this->addedDate = $args['addedDate'];
    $this->authorName = $args['authorName'];
    $this->title = $args['title'];
    $this->url = $args['url'];
  }

  public function getProperties()
  {
    return array(
      'addedDate' => $this->addedDate,
      'authorName' => $this->authorName,
      'title' => $this->title,
      'url' => $this->url,
    );
  }

  public function getJSON()
  {
    return json_encode($this->getProperties());
  }

}