<?php

use PHPUnit\Framework\TestCase;
use App\Services\MessageService;
use App\Repositories\MessageRepository;
use App\Repositories\UserGroupRepository;
use PHPUnit\Framework\MockObject\MockObject;

class MessageServiceTest extends TestCase
{
    /** @var MockObject|MessageRepository */
    private $messageRepository;

    /** @var MockObject|UserGroupRepository */
    private $userGroupRepository;

    /** @var MessageService */
    private $messageService;

    protected function setUp(): void
    {
        $this->messageRepository = $this->createMock(MessageRepository::class);
        $this->userGroupRepository = $this->createMock(UserGroupRepository::class);
        $this->messageService = new MessageService($this->messageRepository, $this->userGroupRepository);
    }

    public function testPostMessageSuccess()
    {


        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 'user1') // groupId = 1, userId = 'user1'
            ->willReturn(true);

        $this->messageRepository->expects($this->once())
            ->method('addMessage')
            ->with(1, 'user1', 'Hello world')
            ->willReturn(true);

        $this->messageService->postMessage(1, 'user1', 'Hello world');
    }

    public function testPostMessageFailure()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 'user1') // groupId = 1, userId = 'user1'
            ->willReturn(true);

        $this->messageRepository->expects($this->once())
            ->method('addMessage')
            ->with(1, 'user1', 'Hello world')
            ->willThrowException(new \Exception('Failed to save message.'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to save message.');

        $this->messageService->postMessage(1, 'user1', 'Hello world');
    }
    public function testGetMessagesSuccess()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 1)
            ->willReturn(true);

        $this->messageRepository->expects($this->once())
            ->method('getMessagesByGroup') // Correct method name
            ->with(1) // groupId = 1
            ->willReturn([
                ['message' => 'Hello world', 'user' => 'user1', 'timestamp' => '2024-11-30']
            ]);

        $messages = $this->messageService->getMessages(1, 1);

        $this->assertCount(1, $messages);
        $this->assertEquals('Hello world', $messages[0]['message']);
    }

    public function testGetMessagesNoMessages()
    {
        $this->userGroupRepository->expects($this->once())
            ->method('isUserInGroup')
            ->with(1, 1)
            ->willReturn(true);

        $this->messageRepository->expects($this->once())
            ->method('getMessagesByGroup') // Correct method name
            ->with(1) // groupId = 1
            ->willReturn([]);

        $messages = $this->messageService->getMessages(1, 1);

        $this->assertCount(0, $messages);
    }
}
