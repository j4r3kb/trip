<?php

declare(strict_types=1);

namespace App\Tests\UserInterface\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    public function testEmployeeIdIsReturnedWithStatus201AsResponseToPostRequest(): void
    {
        $this->client->request(Request::METHOD_POST, '/employee');

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseFormatSame('json');
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertStringContainsString('employeeId', $content);
        $this->assertStringContainsString('_links', $content);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->client->catchExceptions(false);
    }
}
