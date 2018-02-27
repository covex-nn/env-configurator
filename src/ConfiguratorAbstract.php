<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\ServiceConfigurator;

abstract class ConfiguratorAbstract implements ConfiguratorInterface
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $target;

    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return $this
     */
    public function setSource($source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return $this
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }
}
