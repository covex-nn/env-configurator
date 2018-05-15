<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Composer\Semver\Comparator;

/**
 * docker-compose.yaml configurator.
 */
class DockerComposeConfigurator implements ConfiguratorInterface
{
    public const VERSION = '3.2';

    public function apply(string $source, string $target): void
    {
        $sourceDef = new DockerComposeDefinition($source);

        if (file_exists($target)) {
            $targetDef = new DockerComposeDefinition($target);

            if (Comparator::greaterThan($targetDef->getVersion(), $sourceDef->getVersion())) {
                $sourceDef->setVersion($targetDef->getVersion());
            }
            if ($targetDef->hasServices()) {
                foreach ($targetDef->allServices() as $name) {
                    $sourceDef->setService($name, $targetDef->getService($name));
                }
            }
            if ($targetDef->hasVolumes()) {
                foreach ($targetDef->allVolumes() as $name) {
                    $sourceDef->setVolume($name, $targetDef->getVolume($name));
                }
            }
            if ($targetDef->hasNetworks()) {
                foreach ($targetDef->allNetworks() as $name) {
                    $sourceDef->setNetwork($name, $targetDef->getNetwork($name));
                }
            }
        }

        file_put_contents($target, $sourceDef->dump());
    }
}
