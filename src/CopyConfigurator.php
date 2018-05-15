<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

class CopyConfigurator implements ConfiguratorInterface
{
    public function apply(string $source, string $target): void
    {
        $dir = dirname($target);
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new ConfiguratorException(sprintf('%s is not a directory', $dir));
            }
        } else {
            mkdir($dir, 0777, true);
        }
        copy($source, $target);
    }
}
