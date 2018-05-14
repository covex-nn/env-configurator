<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

class DiffConfigurator extends ConfiguratorAbstract
{
    public function apply(): void
    {
        $target = $this->getTarget();
        $data = [];
        if (file_exists($target)) {
            foreach (file($target) as $key => $value) {
                $value = trim($value);
                if (strlen($value)) {
                    $data[] = $value;
                }
            }
            sort($data);
        }

        $diff = file($this->getSource());
        foreach ($diff as $line) {
            $line = trim($line);
            $action = substr($line, 0, 1);
            $line = trim(substr($line, 1));

            if ('-' === $action) {
                if (!strlen($line)) {
                    $data = [];
                } else {
                    $data = array_filter($data, function ($value) use ($line) {
                        return $value !== $line;
                    });
                }
            } elseif ('+' === $action) {
                if (!in_array($line, $data)) {
                    $data[] = $line;
                    sort($data);
                }
            } else {
                throw new ConfiguratorException('Diff lines can start with - or + only');
            }
        }

        $content = implode(PHP_EOL, $data);
        if (count($data)) {
            $content .= PHP_EOL;
        }
        file_put_contents($target, $content);
    }
}
