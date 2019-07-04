<?php

namespace Heyday\Beam\DeploymentProvider;

use Heyday\Beam\Config\DeploymentResultConfiguration;
use Heyday\Beam\Exception\InvalidArgumentException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class DeploymentResult
 * @package Heyday\Beam\DeploymentProvider
 */
class DeploymentResult extends \ArrayObject
{
    /**
     * @var
     */
    protected $updateCounts;

    /**
     * @var DeploymentResultConfiguration
     */
    protected $configuration;

    /**
     * @param $result
     */
    public function __construct(array $result)
    {
        $processor = new Processor();
        $processor->processConfiguration(
            $this->configuration = new DeploymentResultConfiguration(),
            array(
                $result
            )
        );
        parent::__construct($result);
    }

    /**
     * @param string $type
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getUpdateCount($type)
    {
        $types = $this->configuration->getUpdates();
        if (!in_array($type, $types)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Update type '%s' doesn't exist",
                    $type
                )
            );
        }
        if (null === $this->updateCounts) {
            $this->updateCounts = array_fill_keys($this->configuration->getUpdates(), 0);
            foreach ($this as $result) {
                $this->updateCounts[$result['update']]++;
            }
        }

        return $this->updateCounts[$type];
    }

    /**
     * @return DeploymentResultConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
