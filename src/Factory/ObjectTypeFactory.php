<?php

declare(strict_types=1);

namespace GraphQL\Doctrine\Factory;

use GraphQL\Doctrine\Utils;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * A factory to create an ObjectType from a Doctrine entity
 */
class ObjectTypeFactory extends AbstractTypeFactory
{
    /**
     * Create an ObjectType from a Doctrine entity
     * @param string $className class name of Doctrine entity
     * @return ObjectType
     */
    public function create(string $className): Type
    {
        $typeName = Utils::getTypeName($className);
        $description = $this->getDescription($className);

        $fieldGetter = function () use ($className): array {
            $factory = new OutputFieldsConfigurationFactory($this->types, $this->entityManager);
            $configuration = $factory->create($className);

            return $configuration;
        };

        return new ObjectType([
            'name' => $typeName,
            'description' => $description,
            'fields' => $fieldGetter,
        ]);
    }
}
