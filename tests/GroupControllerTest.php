<?php

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use App\Controllers\GroupController;
use App\Services\GroupService;
use PHPUnit\Framework\MockObject\MockObject;
use Slim\Psr7\Response;


class GroupControllerTest extends TestCase
{
    /** @var MockObject|GroupService */
    private $groupService;

    /** @var GroupController */
    private $controller;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->controller = new GroupController($this->groupService);
    }

    public function testCreateGroupSuccess()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups');
        $request = $request->withParsedBody(['groupName' => 'Test Group']);
        $response = new Response();


        $this->groupService->expects($this->once())
            ->method('createGroup')
            ->with('Test Group')
            ->willReturn(1);

        $response = $this->controller->createGroup($request, $response, []);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('Group created successfully.', (string) $response->getBody());
    }

    public function testCreateGroupBadRequest()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups');
        $request = $request->withParsedBody(['groupName' => '']);
        $response = new Response();

        $response = $this->controller->createGroup($request, $response, []);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('error', (string) $response->getBody());
    }

    public function testJoinGroupSuccess()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups/1/join');
        $request = $request->withParsedBody(['user_id' => 'user1']);
        $response = new Response();

        $this->groupService->expects($this->once())
            ->method('joinGroup')
            ->with(1, 'user1')
            ->willReturn(null);

        $response = $this->controller->joinGroup($request, $response, ['groupId' => 1]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('User successfully joined the group.', (string) $response->getBody());
    }

    public function testJoinGroupAlreadyMember()
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/groups/1/join');
        $request = $request->withParsedBody(['user_id' => 'user1']);
        $response = new Response();

        $this->groupService->expects($this->once())
            ->method('joinGroup')
            ->with(1, 'user1')
            ->willThrowException(new \Exception('User is already a member of the group.', 400));

        $response = $this->controller->joinGroup($request, $response, ['groupId' => 1]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('User is already a member of the group.', (string) $response->getBody());
    }

    public function testGetUserGroupsByUsernameSuccess()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/groups/user1');
        $response = new Response();

        $this->groupService->expects($this->once())
            ->method('getUserGroupsByUsername')
            ->with('user1')
            ->willReturn([['groupId' => 1, 'groupName' => 'Test Group']]);

        $response = $this->controller->getUserGroupsByUsername($request, $response, ['username' => 'user1']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Test Group', (string) $response->getBody());
    }

    public function testGetUserGroupsByUsernameNotFound()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/groups/nonexistent');
        $response = new Response();

        $this->groupService->expects($this->once())
            ->method('getUserGroupsByUsername')
            ->with('nonexistent')
            ->willReturn([]);

        $response = $this->controller->getUserGroupsByUsername($request, $response, ['username' => 'nonexistent']);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('No groups found for user.', (string) $response->getBody());
    }
}
