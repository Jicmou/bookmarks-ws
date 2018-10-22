<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class BookmarkDataRetriever
{

  private $logger;

  private $vimeoApiUrl = 'https://vimeo.com/api/oembed.json';

  private $flickrApiUrl = 'http://www.flickr.com/services/oembed/';

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function isVimeo(string $url)
  {
    return (bool)preg_match('/vimeo/', $url);
  }

  public function getApiUrl(string $linkUrl)
  {
    return $this->isVimeo($linkUrl)
      ? $this->vimeoApiUrl . '?url=' . $linkUrl
      : $this->flickrApiUrl . '?format=json&url=' . $linkUrl;
  }

  public function getVimeoBookmarkDataFromResponse($response)
  {
    return array(
      'authorName' => $response->author_name,
      'duration' => $response->duration,
      'height' => $response->height,
      'title' => $response->title,
      'type' => $response->type,
      'url' => $response->provider_url . substr($response->uri, 1),
      'width' => $response->width,
    );

  }

  public function getFlickrBookmarkDataFromResponse($response)
  {
    return array(
      'authorName' => $response->author_name,
      'duration' => null,
      'height' => $response->height,
      'title' => $response->title,
      'type' => $response->type,
      'url' => $response->web_page,
      'width' => $response->width,
    );

  }

  public function retrieveLinkData(string $url)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->getApiUrl($url),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
      ),
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);

    $info = curl_getinfo($curl);

    $this->logger->info('response: ' . $response);
    $this->logger->error('error: ' . $err);
    $this->logger->error('info: ' . json_encode($info));
    curl_close($curl);

    return json_decode($response);
  }

  public function retrieveBookmarkDataFromUrl(string $url)
  {
    return $this->isVimeo($url)
      ? $this->getVimeoBookmarkDataFromResponse(
      $this->retrieveLinkData(($url))
    )
      : $this->getFlickrBookmarkDataFromResponse(
      $this->retrieveLinkData(($url))
    );
  }
}
