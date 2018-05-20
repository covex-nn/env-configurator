<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Covex\Environment\Util\DirectoryUtil;
use Symfony\Component\Yaml\Yaml;

class YamlConfigurator implements ConfiguratorInterface
{
    public function apply(string $source, string $target): void
    {
        $data = Yaml::parseFile($source);

        if (file_exists($target)) {
            $targetData = Yaml::parseFile($target);
            $data = $this->array_merge($targetData, $data);
        }

        DirectoryUtil::createParent($target);
        file_put_contents($target, Yaml::dump($data, 16));
    }

    private function array_merge(array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
