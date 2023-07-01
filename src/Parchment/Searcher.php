<?php

declare(strict_types=1);

namespace Arcanum\Parchment;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Searcher
{
    /**
     * @return iterable<SplFileInfo>
     */
    public static function findAll(string $pattern, string $directoryPath): iterable
    {
        $finder = new Finder();
        foreach ($finder->files()->name($pattern)->in($directoryPath) as $file) {
            yield $file;
        }
    }
}
