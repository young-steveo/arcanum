<?php

declare(strict_types=1);

namespace Arcanum\Test\Quill;

use Arcanum\Quill\Channel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Channel::class)]
final class ChannelTest extends TestCase
{
    public function testEmergency(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('emergency')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->emergency('test');
    }

    public function testAlert(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('alert')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->alert('test');
    }

    public function testCritical(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('critical')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->critical('test');
    }

    public function testError(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('error')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->error('test');
    }

    public function testWarning(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('warning')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->warning('test');
    }

    public function testNotice(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('notice')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->notice('test');
    }

    public function testInfo(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('info')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->info('test');
    }

    public function testDebug(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->createMock(\Monolog\Logger::class);

        $monolog->expects($this->once())
            ->method('debug')
            ->with('test');

        $channel = new Channel($monolog);

        // Act
        $channel->debug('test');
    }

    public function testLog(): void
    {
        // Arrange
        /** @var \Monolog\Logger&\PHPUnit\Framework\MockObject\MockObject */
        $monolog = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $monolog->expects($this->once())
            ->method('log')
            ->with('info', 'test');

        $channel = new Channel($monolog);

        // Act
        $channel->log('info', 'test');
    }
}
