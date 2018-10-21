<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use App\Service\BookmarkDataRetriever;

class BookmarkDataRetrieverTest extends TestCase
{

  private $mockVimeoUrl = 'https://vimeo.com/76979871';
  private $mockFlickrUrl = 'http://www.flickr.com/photos/bees/2341623661/';

  public function getLoggerInterfaceStub()
  {
    $stub = $this->createMock(LoggerInterface::class);
    $stub->method('info');
    $stub->method('error');
    return $stub;
  }

  public function testIsVimeoReturnTrue()
  {
    $testedObject = new BookmarkDataRetriever($this->getLoggerInterfaceStub());
    $this->assertTrue(
      $testedObject->isVimeo($this->mockVimeoUrl),
      'SHOULD return true GIVEN a Vimeo link'
    );
  }

  public function testIsVimeoReturnFalse()
  {
    $testedObject = new BookmarkDataRetriever($this->getLoggerInterfaceStub());
    $linkUrl = 'http://www.flickr.com/photos/bees/2341623661/';
    $this->assertTrue(
      !$testedObject->isVimeo($this->mockFlickrUrl),
      'SHOULD return false GIVEN a non-Vimeo link'
    );
  }

  public function testGetApiUrlReturnsAVimeoUrl()
  {
    $testedObject = new BookmarkDataRetriever($this->getLoggerInterfaceStub());
    $this->assertEquals(
      'https://vimeo.com/api/oembed.json?url=' . $this->mockVimeoUrl,
      $testedObject->getApiUrl($this->mockVimeoUrl),
      'SHOULD return the full Request url GIVEN a Vimeo link'
    );
  }

  public function testGetApiUrlReturnsAFlickrUrl()
  {
    $testedObject = new BookmarkDataRetriever($this->getLoggerInterfaceStub());
    $this->assertEquals(
      'http://www.flickr.com/services/oembed/?format=json&url=' . $this->mockFlickrUrl,
      $testedObject->getApiUrl($this->mockFlickrUrl),
      'SHOULD return the full Request url GIVEN a Vimeo link'
    );
  }

}
