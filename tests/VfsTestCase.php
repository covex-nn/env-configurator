<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Tests;

use Covex\Stream\FileSystem;
use PHPUnit\Framework\TestCase;

abstract class VfsTestCase extends TestCase
{
    protected function setUp(): void
    {
        FileSystem::register('vfs', $this->getVfsRoot());
    }

    protected function tearDown(): void
    {
        FileSystem::unregister('vfs');
    }

    abstract protected function getVfsRoot(): string;
}
