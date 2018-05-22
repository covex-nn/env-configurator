<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Composer\Command\BaseCommand;

interface CommandAwareInterface
{
    /**
     * Set composer command
     */
    public function setCommand(BaseCommand $command);
}
