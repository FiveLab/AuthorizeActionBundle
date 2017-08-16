<?php

declare(strict_types = 1);

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Request\ParamConverter;

use FiveLab\Bundle\AuthorizeActionBundle\Exception\MissingAttributeException;
use FiveLab\Component\AuthorizeAction\Action\AuthorizeActionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Param converter for try create the actions from attributes.
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionParamConverter implements ParamConverterInterface
{
    /**
     * @var PropertyTypeExtractorInterface
     */
    private $propertyTypeExtractor;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * Constructor.
     *
     * @param PropertyTypeExtractorInterface $propertyTypeExtractor
     * @param DenormalizerInterface          $denormalizer
     */
    public function __construct(
        PropertyTypeExtractorInterface $propertyTypeExtractor,
        DenormalizerInterface $denormalizer
    ) {
        $this->propertyTypeExtractor = $propertyTypeExtractor;
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $class = $configuration->getClass();
        $classReflection = new \ReflectionClass($class);
        $options = $configuration->getOptions();

        $object = $classReflection->newInstanceWithoutConstructor();

        if (array_key_exists('fields', $options)) {
            $this->setPropertyValuesToObject($request, $object, $options['fields']);
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getConverter() === 'authorize_action' &&
            is_a($configuration->getClass(), AuthorizeActionInterface::class, true);
    }

    /**
     * Set the values to object
     *
     * @param Request $request
     * @param object  $object
     * @param array   $fields
     *
     * @throws MissingAttributeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function setPropertyValuesToObject(Request $request, $object, array $fields): void
    {
        $objectClass = get_class($object);

        foreach ($fields as $propertyName => $attributeName) {
            $value = $request->attributes->get($attributeName);
            $propertyTypes = $this->propertyTypeExtractor->getTypes($objectClass, $propertyName);
            $propertyType = null;

            if ($propertyTypes) {
                $propertyType = array_pop($propertyTypes);
            }

            if (!$value) {
                // The value is empty. Throw exception if value cannot be nullable.
                if ($propertyType && !$propertyType->isNullable()) {
                    throw new MissingAttributeException(sprintf(
                        'Missing the attribute with name "%s" in request.',
                        $attributeName
                    ));
                }

                // We cannot get the type of property or property can be nullable.
                continue;
            }

            if ($propertyType->getBuiltinType() === Type::BUILTIN_TYPE_OBJECT) {
                // The property should be an object. Try denormalize.
                $value = $this->denormalizer->denormalize($value, $propertyType->getClassName());
            }

            $refProperty = new \ReflectionProperty($object, $propertyName);
            $refProperty->setAccessible(true);
            $refProperty->setValue($object, $value);
        }
    }
}
