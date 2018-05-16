<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Tests\Composer;

use Covex\Environment\Composer\ApplyCommand;
use Covex\Environment\Configurator\ConfiguratorException;
use Covex\Environment\Configurator\ConfiguratorInterface;
use Covex\Environment\Configurator\CopyConfigurator;
use Covex\Environment\Tests\VfsTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ApplyCommandTest extends VfsTestCase
{
    public function testAddConfigurator(): void
    {
        $command = new ApplyCommand();

        $configurator = new class() implements ConfiguratorInterface {
            public function apply(string $source, string $target): void
            {
            }
        };

        $this->assertFalse($command->hasConfigurator('qwerty'));
        $command->addConfigurator('qwerty', $configurator);
        $this->assertTrue($command->hasConfigurator('qwerty'));
        $this->assertEquals($configurator, $command->getConfigurator('qwerty'));
    }

    /**
     * @dataProvider providerBadRepository
     */
    public function testBadRepository($repository): void
    {
        $this->expectException(ConfiguratorException::class);

        $command = new ApplyCommand();
        $command->addRepository($repository);
    }

    public function providerBadRepository(): array
    {
        return [
            [__FILE__],
            [__DIR__.DIRECTORY_SEPARATOR.'not-exists'],
        ];
    }

    public function testAddRepository(): void
    {
        $command = new ApplyCommand();
        $this->assertSame([], $command->getRepositories());

        $command->addRepository('vfs://repo1');
        $this->assertSame(['vfs://repo1'], $command->getRepositories());
        $command->addRepository('vfs://repo2');
        $this->assertSame(['vfs://repo1', 'vfs://repo2'], $command->getRepositories());
    }

    public function testRequire(): void
    {
        $input = new ArrayInput([
            'package' => 'asdf',
            'target' => 'vfs://target',
        ]);

        $command = new ApplyCommand();
        $command
            ->addRepository('vfs://repo1')
            ->addRepository('vfs://repo2')
            ->addRepository('vfs://repo3')
            ->addConfigurator('copy', new CopyConfigurator());

        $command->run($input, new NullOutput());

        $this->assertFileExists('vfs://target/target1.txt');
        $this->assertEquals('source-from-repo2', file_get_contents('vfs://target/target1.txt'));
        $this->assertFileExists('vfs://target/target2.txt');
        $this->assertEquals('source-from-repo3', file_get_contents('vfs://target/target2.txt'));
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'repository';
    }
}
