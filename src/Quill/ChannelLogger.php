<?php

declare(strict_types=1);

namespace Arcanum\Quill;

interface ChannelLogger
{
    public function channel(string $name): Channel;
}
