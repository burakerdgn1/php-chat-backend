<?php

use PHPUnit\Framework\TestCase;
use App\Services\GroupService;
use App\Repositories\GroupRepository;
use App\Repositories\UserGroupRepository;
use PHPUnit\Framework\MockObject\MockObject;



class GroupServiceTest extends TestCase
{
    /** @var MockObject|GroupRepository */
    private $groupRepository;

    /** @var MockObject|UserGroupRepository */
    private $userGroupRepository;

    /** @var GroupService */
    private $groupService;

    protected function setUp(): void
    {
        $this->groupRepository = $this->createMock(GroupRepository::class);
        $this->userGroupRepository = $this->createMock(UserGroupRepository::class);
        $this->groupService = new GroupService($this->groupRepository, $this->userGroupRepository);
    }

    public function testJoinGroupSuccess()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 'user1')
            ->willReturn(false);

        $this->userGroupRepository->expects($this->once())
            ->method('addUserToGroup')
            ->with(1, 'user1');

        $this->groupService->joinGroup(1, 'user1');
    }

    public function testJoinGroupAlreadyMember()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 'user1')
            ->willReturn(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User is already a member of the group.');

        $this->groupService->joinGroup(1, 'user1');
    }
    public function testCreateGroupSuccess()
    {
        $this->groupRepository->expects($this->once())
            ->method('create')
            ->with('Test Group')
            ->willReturn(1);

        $groupId = $this->groupService->createGroup('Test Group');

        $this->assertEquals(1, $groupId);
    }

    public function testCreateGroupFailure()
    {
        $this->groupRepository->expects($this->once())
            ->method('create')
            ->with('Invalid Group')
            ->willThrowException(new \Exception('Group creation failed.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Group creation failed.');

        $this->groupService->createGroup('Invalid Group');
    }

    public function testGetUserGroupsByUsernameSuccess()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('getUserGroupsByUsername')
            ->with('user1')
            ->willReturn([['id' => 1, 'name' => 'Test Group']]);

        $groups = $this->groupService->getUserGroupsByUsername('user1');

        $this->assertCount(1, $groups);
        $this->assertEquals('Test Group', $groups[0]['name']);
    }

    public function testGetUserGroupsByUsernameNotFound()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('getUserGroupsByUsername')
            ->with('nonexistent')
            ->willReturn([]);

        $groups = $this->groupService->getUserGroupsByUsername('nonexistent');

        $this->assertCount(0, $groups);
    }
}
