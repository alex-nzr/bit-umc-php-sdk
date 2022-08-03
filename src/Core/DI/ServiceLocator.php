<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * bit-umc-php-sdk - ServiceLocator.php
 * 04.08.2022 00:20
 * ==================================================
 */

namespace ANZ\BitUmc\SDK\Core\DI;

use ANZ\BitUmc\SDK\Core\Trait\Singleton;
use Closure;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ServiceLocator
 * @package ANZ\BitUmc\SDK\Core\DI
 * @method static ServiceLocator getInstance()
 */
class ServiceLocator implements ContainerInterface
{
    use Singleton;

    private array $services = [];
    private array $store    = [];

    /**
     * @param string $id
     * @param string $className
     * @param array $params
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function add(string $id, string $className, array $params = []): void
    {
        if (empty($id) || empty($className))
        {
            throw $this->buildBadRegistrationExceptions($id);
        }
        $this->services[$id] = [$className, $params];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->store[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        if (isset($this->store[$id]))
        {
            return $this->store[$id];
        }

        if (!isset($this->services[$id]))
        {
            throw $this->buildNotFoundException($id);
        }

        [$class, $args] = $this->services[$id];

        if ($class instanceof Closure)
        {
            $object = $class();
        }
        else
        {
            if ($args instanceof Closure)
            {
                $args = $args();
            }
            $object = new $class(...array_values($args));
        }

        $this->store[$id] = $object;

        return $object;
    }

    /**
     * @param string $id
     * @return \Exception|\Psr\Container\NotFoundExceptionInterface
     */
    private function buildNotFoundException(string $id): Exception|NotFoundExceptionInterface
    {
        $message = "Could not find service by id $id.";
        return new class($message) extends Exception implements NotFoundExceptionInterface{};
    }

    private function buildBadRegistrationExceptions(string $id): ContainerExceptionInterface|Exception
    {
        $message = "Could not register service {$id}.";
        return new class($message) extends Exception implements ContainerExceptionInterface{};
    }
}