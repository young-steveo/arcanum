<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Fixture;

final class DoSomethingHandler
{
    public function __invoke(DoSomething $command): DoSomethingResult
    {
        return new DoSomethingResult($command->name);
    }
}
