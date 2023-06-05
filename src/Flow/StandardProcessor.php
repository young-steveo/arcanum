<?php

declare(strict_types=1);

namespace Arcanum\Flow;

class StandardProcessor implements Processor
{
    /**
     * Process a payload through a series of stages.
     *
     * @param callable(object, callable): (object|null) $stages
     */
    public function process(object $payload, callable ...$stages): object
    {
        $stages = array_reverse($stages);
        $next = fn(object $payload): object => $payload;
        foreach ($stages as $stage) {
            $next = $this->wrapNext($stage, $next);
        }
        return $next($payload);
    }

    /**
     * @param callable(object, callable): (object|null) $stage
     * @param callable(object): object $next
     */
    protected function wrapNext(callable $stage, callable $next): callable
    {
        return function (object $payload) use ($stage, $next): object {
            $called = false;
            $callback = function () use (&$called, $next, $payload): object {
                $called = true;
                return $next($payload);
            };

            $result = $stage($payload, $callback);

            // if the callback was called, we should return the payload.
            if ($called) {
                return $payload;
            }

            // if the callback was not called, but the stage returned an object,
            // we whould pass that object on to the next stage
            // by returning it.
            if ($result) {
                return $next($result);
            }
            // if the callback was not called, and the stage did not return anything,
            // we have halted the pipeline and we should bail out.
            throw new Interrupted("Stage did not call next or return a payload.");
        };
    }
}
