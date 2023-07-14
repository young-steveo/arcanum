<?php

declare(strict_types=1);

namespace Arcanum\Quill;

enum Handler : string
{
    /**
     * File handlers
     */
    case STREAM = 'stream';
    case ROTATING_FILE = 'rotating_file';
    case SYSLOG = 'syslog';
    case ERROR_LOG = 'error_log';
    case PROCESS = 'process';

    /**
     * @todo Add more support for other built-in MonoLog handlers.
     */
}
