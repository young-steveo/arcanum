<?php

declare(strict_types=1);

namespace Arcanum\Hyper\Files;

enum Error: int
{
    case UPLOAD_ERR_OK = \UPLOAD_ERR_OK;
    case UPLOAD_ERR_INI_SIZE = \UPLOAD_ERR_INI_SIZE;
    case UPLOAD_ERR_FORM_SIZE = \UPLOAD_ERR_FORM_SIZE;
    case UPLOAD_ERR_PARTIAL = \UPLOAD_ERR_PARTIAL;
    case UPLOAD_ERR_NO_FILE = \UPLOAD_ERR_NO_FILE;
    case UPLOAD_ERR_NO_TMP_DIR = \UPLOAD_ERR_NO_TMP_DIR;
    case UPLOAD_ERR_CANT_WRITE = \UPLOAD_ERR_CANT_WRITE;
    case UPLOAD_ERR_EXTENSION = \UPLOAD_ERR_EXTENSION;

    public static function fromErrorCode(int $code): Error
    {
        return match ($code) {
            \UPLOAD_ERR_OK => self::UPLOAD_ERR_OK,
            \UPLOAD_ERR_INI_SIZE => self::UPLOAD_ERR_INI_SIZE,
            \UPLOAD_ERR_FORM_SIZE => self::UPLOAD_ERR_FORM_SIZE,
            \UPLOAD_ERR_PARTIAL => self::UPLOAD_ERR_PARTIAL,
            \UPLOAD_ERR_NO_FILE => self::UPLOAD_ERR_NO_FILE,
            \UPLOAD_ERR_NO_TMP_DIR => self::UPLOAD_ERR_NO_TMP_DIR,
            \UPLOAD_ERR_CANT_WRITE => self::UPLOAD_ERR_CANT_WRITE,
            \UPLOAD_ERR_EXTENSION => self::UPLOAD_ERR_EXTENSION,
            default => throw new \InvalidArgumentException("Invalid error code: $code"),
        };
    }

    public function isOK(): bool
    {
        return $this->value === \UPLOAD_ERR_OK;
    }
}
