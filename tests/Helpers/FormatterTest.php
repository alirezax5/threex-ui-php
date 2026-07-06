<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Helpers\Formatter;

class FormatterTest extends TestCase
{
    public function test_bytes_to_human(): void
    {
        $this->assertSame('0 B', Formatter::bytesToHuman(0));
        $this->assertSame('1 B', Formatter::bytesToHuman(1));
        $this->assertSame('1 KB', Formatter::bytesToHuman(1024));
        $this->assertSame('1 MB', Formatter::bytesToHuman(1048576));
        $this->assertSame('1 GB', Formatter::bytesToHuman(1073741824));
        $this->assertSame('1.5 GB', Formatter::bytesToHuman(1610612736));
        $this->assertSame('1 TB', Formatter::bytesToHuman(1099511627776));
    }

    public function test_human_to_bytes(): void
    {
        $this->assertSame(0, Formatter::humanToBytes('0'));
        $this->assertSame(1024, Formatter::humanToBytes('1 KB'));
        $this->assertSame(1048576, Formatter::humanToBytes('1 MB'));
        $this->assertSame(1073741824, Formatter::humanToBytes('1 GB'));
        $this->assertSame(1099511627776, Formatter::humanToBytes('1 TB'));
    }

    public function test_gb_to_bytes(): void
    {
        $this->assertSame(1073741824, Formatter::gbToBytes(1));
        $this->assertSame(5368709120, Formatter::gbToBytes(5));
        $this->assertSame(0, Formatter::gbToBytes(0));
    }

    public function test_bytes_to_gb(): void
    {
        $this->assertSame(1.0, Formatter::bytesToGb(1073741824));
        $this->assertSame(5.0, Formatter::bytesToGb(5368709120));
        $this->assertSame(0.0, Formatter::bytesToGb(0));
    }

    public function test_expiry_time_unlimited(): void
    {
        $this->assertSame('unlimited', Formatter::expiryTime(0));
    }

    public function test_truncate(): void
    {
        $this->assertSame('Hello...', Formatter::truncate('Hello World', 8));
        $this->assertSame('Hello World', Formatter::truncate('Hello World', 50));
        $this->assertSame('', Formatter::truncate('', 10));
    }

    public function test_sanitize(): void
    {
        $this->assertSame('&lt;script&gt;', Formatter::sanitize('<script>'));
        $this->assertSame('hello &amp; world', Formatter::sanitize('hello & world'));
    }
}
