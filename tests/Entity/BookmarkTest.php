<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Bookmark;

class BookmarkTest extends TestCase
{

  private $mockProperties = array(
    'addedDate' => '2018-11-10 11:00',
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

  public function testCreate()
  {
    $bookmark = $this->createMockBookmark($this->mockProperties);
    $this->assertEquals($this->mockProperties, $bookmark->getProperties());
  }

}