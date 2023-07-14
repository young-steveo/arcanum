<?php

declare(strict_types=1);

namespace Arcanum\Quill;

use Psr\Log\LoggerInterface;

/**
 * Quill Channel
 * -------------
 *
 * Quill Channels are PSR-3 compliant loggers that wrap a
 * single Monolog logger instance.
 */
class Channel implements LoggerInterface
{
    /**
     * The name of the channel.
     */
    public readonly string $name;

    /**
     * Create a new channel.
     *
     * We typehint the logger as a Monolog logger rather than a PSR-3 logger
     * becaue we want to be able to get the channel name from the logger which
     * is not a method defined by PSR-3.
     */
    public function __construct(
        private \Monolog\Logger $logger,
    ) {
        $this->name = $logger->getName();
    }

    /**
     * System is unusable.
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param 'alert'|'critical'|'debug'|'emergency'|'error'|'info'|'notice'|'warning'|\Monolog\Level $level
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
