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
class DockerCompose extends ConfiguratorAbstract
{
    public const VERSION = '3.2';

    public function apply(): void
    {
        $sourcePath = $this->getSource();
        $targetPath = $this->getTarget();

        $source = new DockerComposeDefinition($sourcePath);

        if (file_exists($targetPath)) {
            $target = new DockerComposeDefinition($targetPath);

            if (Comparator::greaterThan($target->getVersion(), $source->getVersion())) {
                $source->setVersion($target->getVersion());
            }
            if ($target->hasServices()) {
                foreach ($target->allServices() as $name) {
                    $source->setService($name, $target->getService($name));
                }
            }
            if ($target->hasVolumes()) {
                foreach ($target->allVolumes() as $name) {
                    $source->setVolume($name, $target->getVolume($name));
                }
            }
            if ($target->hasNetworks()) {
                foreach ($target->allNetworks() as $name) {
                    $source->setNetwork($name, $target->getNetwork($name));
                }
            }
        }

        file_put_contents($targetPath, $source->dump());
    }
}
