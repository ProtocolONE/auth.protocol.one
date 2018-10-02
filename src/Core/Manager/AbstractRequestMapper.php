<?php

namespace Core\Manager;

/**
 * Class AbstractFormMapManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
abstract class AbstractRequestMapper
{
    const OPTION_MAP_TO = 'map_to';
    const OPTION_MAP_SKIP = 'map_skip';

    /**
     * @var object
     */
    private $object;

    /**
     * @param object $object
     * @return AbstractRequestMapper
     */
    public function setObject($object): AbstractRequestMapper
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * @return object
     * @throws \ReflectionException
     */
    public function map()
    {
        $options = $this->getOptions();
        $properties = (new \ReflectionClass($this))->getProperties();

        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $name = $property->getName();
            $setter = 'set'.ucfirst($name);
            $getter = 'get'.ucfirst($name);

            if (isset($options[$name])) {
                if (isset($options[$name][self::OPTION_MAP_SKIP])
                    && true === $options[$name][self::OPTION_MAP_SKIP]) {
                    continue;
                }

                if (isset($options[$name][self::OPTION_MAP_TO])) {
                    $setter = 'set'.ucfirst($options[$name][self::OPTION_MAP_TO]);
                }
            }

            if (false === method_exists($this->object, $setter)
                || false === method_exists($this, $getter)) {
                continue;
            }

            $this->object->{$setter}($this->{$getter}());
        }

        return $this->object;
    }
}