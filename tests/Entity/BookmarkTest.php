<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Bookmark;
use App\Entity\Tag;

class BookmarkTest extends TestCase
{

  private $mockProperties = array(
    'authorName' => 'foo',
    'duration' => 456,
    'height' => 400,
    'title' => 'bar',
    'type' => 'video',
    'url' => 'http://www.baz.qux',
    'width' => 600,
  );

  private function createMockBookmark(array $args)
  {
    $bookmark = new Bookmark();
    $bookmark->create($args);
    return $bookmark;
  }

  private function createTag(string $name)
  {
    $tag = new Tag();
    $tag->setName($name);
    return $tag;
  }

  public function testCreate()
  {
    $bookmark = $this->createMockBookmark($this->mockProperties);
    $this->assertEquals($this->mockProperties['authorName'], $bookmark->getProperties()['authorName'], 'SHOULD create Object with right authorName');
    $this->assertEquals($this->mockProperties['duration'], $bookmark->getProperties()['duration'], 'SHOULD create Object with right duration');
    $this->assertEquals($this->mockProperties['height'], $bookmark->getProperties()['height'], 'SHOULD create Object with right height');
    $this->assertEquals($this->mockProperties['title'], $bookmark->getProperties()['title'], 'SHOULD create Object with right title');
    $this->assertEquals($this->mockProperties['type'], $bookmark->getProperties()['type'], 'SHOULD create Object with right type');
    $this->assertEquals($this->mockProperties['url'], $bookmark->getProperties()['url'], 'SHOULD create Object with right url');
    $this->assertEquals($this->mockProperties['width'], $bookmark->getProperties()['width'], 'SHOULD create Object with right width');
  }

  public function testGetProperties()
  {
    $bookmark = $this->createMockBookmark($this->mockProperties);
    $tagFoo = $this->createTag('foo');
    $bookmark->addTag($tagFoo);
    $this->assertEquals($tagFoo->getEndpointFromTag(), $bookmark->getProperties()['tags'][0], 'SHOULD return a list of related tag endpoints');
  }

}