<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\GroupService;

class GroupController
{
    private $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function createGroup(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {

            $groupName = trim($data['groupName']);
            $this->groupService->createGroup($groupName);

            $response->getBody()->write(json_encode(['message' => 'Group created successfully.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function joinGroup(Request $request, Response $response, $args)
    {
        $groupId = $args['groupId'];
        $data = $request->getParsedBody();

        try {

            $userId = trim($data['user_id']);
            $this->groupService->joinGroup($groupId, $userId);

            $response->getBody()->write(json_encode(['message' => 'User successfully joined the group.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Method to fetch all groups
    public function getAllGroups(Request $request, Response $response, $args)
    {
        try {

            $groups = $this->groupService->getAllGroups();
            $response->getBody()->write(json_encode($groups));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getUserGroupsByUsername(Request $request, Response $response, $args)
    {
        $username = $args['username'];

        try {

            $groups = $this->groupService->getUserGroupsByUsername($username);

            $response->getBody()->write(json_encode(['groups' => $groups]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}