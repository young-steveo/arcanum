<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\Files;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\Files\Error;

#[CoversClass(Error::class)]
final class ErrorTest extends TestCase
{
    public function testErrorFrom(): void
    {
        // Arrange
        $oka = Error::from(\UPLOAD_ERR_OK);
        $ini = Error::from(\UPLOAD_ERR_INI_SIZE);
        $for = Error::from(\UPLOAD_ERR_FORM_SIZE);
        $par = Error::from(\UPLOAD_ERR_PARTIAL);
        $fil = Error::from(\UPLOAD_ERR_NO_FILE);
        $tmp = Error::from(\UPLOAD_ERR_NO_TMP_DIR);
        $wri = Error::from(\UPLOAD_ERR_CANT_WRITE);
        $ext = Error::from(\UPLOAD_ERR_EXTENSION);

        // Assert
        $this->assertEquals($oka->value, \UPLOAD_ERR_OK);
        $this->assertEquals($ini->value, \UPLOAD_ERR_INI_SIZE);
        $this->assertEquals($for->value, \UPLOAD_ERR_FORM_SIZE);
        $this->assertEquals($par->value, \UPLOAD_ERR_PARTIAL);
        $this->assertEquals($fil->value, \UPLOAD_ERR_NO_FILE);
        $this->assertEquals($tmp->value, \UPLOAD_ERR_NO_TMP_DIR);
        $this->assertEquals($wri->value, \UPLOAD_ERR_CANT_WRITE);
        $this->assertEquals($ext->value, \UPLOAD_ERR_EXTENSION);
    }

    public function testIsOK(): void
    {
        // Arrange
        $oka = Error::from(\UPLOAD_ERR_OK);
        $ini = Error::from(\UPLOAD_ERR_INI_SIZE);

        // Assert
        $this->assertTrue($oka->isOK());
        $this->assertFalse($ini->isOK());
    }
}
