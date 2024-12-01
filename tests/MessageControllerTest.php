<?php

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use App\Controllers\MessageController;
use App\Services\MessageService;
use PHPUnit\Framework\MockObject\MockObject;
use Slim\Psr7\Response;


class MessageControllerTest extends TestCase
{
    /** @var MockObject|MessageService */
    private $messageService;

    /** @var MessageController */
    private $controller;

    protected function setUp(): void
    {
        $this->messageService = $this->createMock(MessageService::class);
        $this->controller = new MessageController($this->messageService);
    }

    public function testPostMessageSuccess()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups/1');
        $request = $request->withParsedBody(['user_id' => 'user1', 'message' => 'Hello']);
        $response = new Response();

        $this->messageService->expects($this->once())
            ->method('postMessage')
            ->with(1, 'user1', 'Hello')
            ->willReturn(null);

        $response = $this->controller->postMessage($request, $response, ['groupId' => 1]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('Message posted successfully.', (string) $response->getBody());
    }

    public function testPostMessageForbidden()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups/1');
        $request = $request->withParsedBody(['user_id' => 'user2', 'message' => 'Unauthorized']);
        $response = new Response();

        $this->messageService->expects($this->once())
            ->method('postMessage')
            ->with(1, 'user2', 'Unauthorized')
            ->willThrowException(new \Exception('User is not a member of this group.', 403));

        $response = $this->controller->postMessage($request, $response, ['groupId' => 1]);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('User is not a member of this group.', (string) $response->getBody());
    }

    public function testGetMessagesSuccess()
{
    $request = (new ServerRequestFactory())->createServerRequest('GET', '/groups/1/messages');
    $response = new Response();

    $this->messageService->expects($this->once())
        ->method('getMessages')
        ->with(1)
        ->willReturn([
            ['message' => 'Hello world', 'user' => 'user1', 'timestamp' => '2024-11-30']
        ]);

    $response = $this->controller->getMessages($request, $response, ['groupId' => 1]);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertStringContainsString('Hello world', (string) $response->getBody());
}

public function testGetMessagesNoMessages()
{
    $request = (new ServerRequestFactory())->createServerRequest('GET', '/groups/1/messages');
    $response = new Response();

    $this->messageService->expects($this->once())
        ->method('getMessages')
        ->with(1)
        ->willReturn([]);

    $response = $this->controller->getMessages($request, $response, ['groupId' => 1]);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertStringContainsString('No messages found.', (string) $response->getBody());
}
}


