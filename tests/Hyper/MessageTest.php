<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper;

use Arcanum\Hyper\Message;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Message::class)]
final class MessageTest extends TestCase
{
    public function testMessage(): void
    {
        $this->markTestSkipped('Not implemented yet.');
    }
}
