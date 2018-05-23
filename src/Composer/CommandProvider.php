<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Covex\Environment\Configurator\ComposerConfigurator;
use Covex\Environment\Configurator\CopyConfigurator;
use Covex\Environment\Configurator\DiffConfigurator;
use Covex\Environment\Configurator\DockerComposeConfigurator;
use Covex\Environment\Configurator\ReplaceConfigurator;
use Covex\Environment\Configurator\YamlConfigurator;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        $apply = new ApplyCommand();
        $apply
            ->addRepository(dirname(__DIR__).'/Resources/repository')
            ->addConfigurator('copy', new CopyConfigurator())
            ->addConfigurator('diff', new DiffConfigurator())
            ->addConfigurator('docker-compose', new DockerComposeConfigurator())
            ->addConfigurator('replace', new ReplaceConfigurator())
            ->addConfigurator('yaml', new YamlConfigurator())
            ->addConfigurator('composer', new ComposerConfigurator())
        ;

        return [$apply];
    }
}
