<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Service\JSONResponse;
use PHPUnit\Framework\Constraint\Exception;

class JSONResponseTest extends TestCase
{
  public function testGetJSONResponse()
  {
    $body = json_encode(
      array(
        'foo' => 'bar'
      )
    );
    $status = 200;
    $testedObject = new JSONResponse();
    $actualResult = $testedObject->getJSONResponse($body, $status);
    $this->assertEquals($body, $actualResult->getContent());
    $this->assertEquals($status, $actualResult->getStatusCode());
  }

  public function testGetSuccessResponse()
  {
    $body = json_encode(
      array(
        'foo' => 'bar'
      )
    );
    $testedObject = new JSONResponse();
    $actualResult = $testedObject->getSuccessResponse($body);
    $this->assertEquals(
      $body,
      $actualResult->getContent(),
      'SHOULD return GIVEN json object as a Response content body'
    );
    $this->assertEquals(
      200,
      $actualResult->getStatusCode(),
      'SHOULD return 200 status code'
    );
  }

  public function testGetNoContentResponse()
  {
    $testedObject = new JSONResponse();
    $actualResult = $testedObject->getNoContentResponse();
    $this->assertEmpty(
      $actualResult->getContent(),
      'SHOULD return an empty body'
    );
    $this->assertEquals(
      204,
      $actualResult->getStatusCode(),
      'SHOULD return 204 status code'
    );
  }

  public function testGetInternalErrorResponse()
  {
    $message = 'foo';
    $testedObject = new JSONResponse();
    $expectedResult = json_encode(
      array(
        'code' => 500,
        'message' => $message
      )
    );
    $actualResult = $testedObject->getInternalErrorResponse(new \Exception($message));
    $this->assertEquals(
      500,
      $actualResult->getStatusCode(),
      'Status code SHOULD be 500'
    );
    $this->assertEquals(
      $expectedResult,
      $actualResult->getContent(),
      'GIVEN message and 500 status code SHOULD be in the Response content body'
    );
  }

  public function testGetRequestErrorResponse()
  {
    $message = 'foo';
    $testedObject = new JSONResponse();
    $expectedResult = json_encode(
      array(
        'code' => 400,
        'message' => $message
      )
    );
    $actualResult = $testedObject->getRequestErrorResponse($message);
    $this->assertEquals(
      400,
      $actualResult->getStatusCode(),
      'Status code SHOULD be 400'
    );
    $this->assertEquals(
      $expectedResult,
      $actualResult->getContent(),
      'GIVEN message and 400 status code SHOULD be in the Response content body'
    );
  }

  public function testGetNotFoundErrorResponse()
  {
    $testedObject = new JSONResponse();
    $expectedResult = json_encode(
      array(
        'code' => 404,
        'message' => 'Ressource not found'
      )
    );
    $actualResult = $testedObject->getNotFoundErrorResponse();
    $this->assertEquals(
      404,
      $actualResult->getStatusCode(),
      'Status code SHOULD be 404'
    );
    $this->assertEquals(
      $expectedResult,
      $actualResult->getContent(),
      'GIVEN message and 404 status code SHOULD be in the Response content body'
    );
  }
}
