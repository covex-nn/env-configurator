<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CapabilityCommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class EnvironmentPluginDebug implements PluginInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function getCapabilities(): array
    {
        return [
            CapabilityCommandProvider::class => CommandProvider::class,
        ];
    }
}
