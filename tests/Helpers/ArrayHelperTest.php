<?php

declare(strict_types=1);

namespace ThreeXUI\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use ThreeXUI\Helpers\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    public function test_dot_get_simple(): void
    {
        $data = ['name' => 'John', 'age' => 30];

        $this->assertSame('John', ArrayHelper::dotGet($data, 'name'));
        $this->assertSame(30, ArrayHelper::dotGet($data, 'age'));
    }

    public function test_dot_get_nested(): void
    {
        $data = ['user' => ['profile' => ['email' => 'test@example.com']]];

        $this->assertSame('test@example.com', ArrayHelper::dotGet($data, 'user.profile.email'));
    }

    public function test_dot_get_missing_key_returns_default(): void
    {
        $data = ['a' => 1];

        $this->assertNull(ArrayHelper::dotGet($data, 'b'));
        $this->assertSame('fallback', ArrayHelper::dotGet($data, 'b', 'fallback'));
    }

    public function test_dot_get_empty_key_returns_array(): void
    {
        $data = ['a' => 1, 'b' => 2];

        $this->assertSame($data, ArrayHelper::dotGet($data, ''));
    }

    public function test_dot_set(): void
    {
        $data = [];
        ArrayHelper::dotSet($data, 'user.name', 'John');
        ArrayHelper::dotSet($data, 'user.age', 30);

        $this->assertSame(['user' => ['name' => 'John', 'age' => 30]], $data);
    }

    public function test_dot_has(): void
    {
        $data = ['a' => ['b' => ['c' => 'value']]];

        $this->assertTrue(ArrayHelper::dotHas($data, 'a.b.c'));
        $this->assertTrue(ArrayHelper::dotHas($data, 'a.b'));
        $this->assertFalse(ArrayHelper::dotHas($data, 'a.b.x'));
        $this->assertFalse(ArrayHelper::dotHas($data, 'x.y'));
    }

    public function test_remove_empty(): void
    {
        $data = ['a' => 'ok', 'b' => null, 'c' => '', 'd' => []];
        $result = ArrayHelper::removeEmpty($data);

        $this->assertSame(['a' => 'ok'], $result);
    }

    public function test_only(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = ArrayHelper::only($data, ['a', 'c']);

        $this->assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function test_except(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = ArrayHelper::except($data, ['b']);

        $this->assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function test_to_query(): void
    {
        $result = ArrayHelper::toQuery(['name' => 'John', 'age' => 30]);

        $this->assertSame('name=John&age=30', urldecode($result));
    }
}
