<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

/**
 * Hyper Server Adapter
 * --------------------
 *
 * ServerAdapter offers multiple wrappers for built-in, global PHP functions
 * that are used in the request-response lifecycle.
 */
interface ServerAdapter
{
    /**
     * @return array<string,string> The HTTP header key/value pairs.
     */
    public function getallheaders(): array;

    /**
     * Checks if or where headers have been sent.
     *
     * You can't add any more header lines using the header() function once the
     * header block has already been sent. Using this function you can at least
     * prevent getting HTTP header related error messages.
     */
    public function headersSent(string|null &$file = null, int|null &$line = null): bool;

    /**
     * Send a raw HTTP header.
     *
     * Remember that header() must be called before any actual output is sent
     */
    public function header(string $header, bool $replace = true, int $responseCode = 0): void;

    /**
     * Echo content to the client.
     */
    public function echo(string $content): void;

    /**
     * Should call fastcgi_finish_request() if possible, and should return
     * false if fastcgi_finish_request() is not available.
     */
    public function fastCGIFinishRequest(): bool;

    /**
     * Should call litespeed_finish_request() if possible, and should return
     * false if litespeed_finish_request() is not available.
     */
    public function litespeedFinishRequest(): bool;

    /**
     * Flush the system write buffers.
     */
    public function flush(): void;

    /**
     * Get status information on either the top level output buffer or all
     * active output buffer levels if $fullStatus is true.
     *
     * @return array{
     *   level: int,
     *   type?: 0|1,
     *   status?: int,
     *   name?: string,
     *   del?: int
     * }|(array<int, array{
     *   type?: 0|1,
     *   status?: int,
     *   name?: string,
     *   del?: int,
     *   chunk_size?: int,
     *   size?: int,
     *   block_size?: int,
     * }>)
     */
    public function obGetStatus(bool $fullStatus = false): array;

    /**
     * Call ob_end_flush()
     */
    public function obEndFlush(): bool;

    /**
     * Call ob_end_clean()
     */
    public function obEndClean(): bool;
}
