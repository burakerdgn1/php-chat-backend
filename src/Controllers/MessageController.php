<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\MessageService;
use App\Validators\MessageValidator;

class MessageController
{
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    // Method to post a message to a group
    public function postMessage(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        $data = $request->getParsedBody();

        try {

            $userId = trim($data['user_id']);
            $messageContent = trim($data['message']);

            $this->messageService->postMessage($groupId, $userId, $messageContent);

            $response->getBody()->write(json_encode(['message' => 'Message posted successfully.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
    }

    // Method to fetch messages for a group
    public function getMessages(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        $userName = $args['userId'];

        try {

            $userId = trim($userName);
            $messages = $this->messageService->getMessages($groupId, $userId);

            $response->getBody()->write(json_encode($messages));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getCode() ?: 500);
        }
    }
}
