<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

class CopyConfigurator extends ConfiguratorAbstract
{
    public function apply(): void
    {
        $source = $this->getSource();
        $target = $this->getTarget();

        if (file_exists($target)) {
            if (!is_dir($target)) {
                throw new ConfiguratorException(sprintf('%s is not a directory', $target));
            }
        } else {
            mkdir($target, 0777, true);
        }
        copy($source, $target.'/'.basename($source));
    }
}
