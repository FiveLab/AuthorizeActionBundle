<?php

/*
 * This file is part of the FiveLab AuthorizeActionBundle package
 *
 * (c) FiveLab
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FiveLab\Bundle\AuthorizeActionBundle\Tests\Request\ParamConverter;

use FiveLab\Bundle\AuthorizeActionBundle\Request\ParamConverter\AuthorizeActionParamConverter;
use FiveLab\Component\AuthorizeAction\Action\AuthorizeActionInterface;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class AuthorizeActionParamConverterTest extends TestCase
{
    /**
     * @var PropertyTypeExtractorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $propertyTypeExtractor;

    /**
     * @var DenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $denormalizer;

    /**
     * @var AuthorizeActionParamConverter
     */
    private $converter;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->propertyTypeExtractor = $this->createMock(PropertyTypeExtractorInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->converter = new AuthorizeActionParamConverter($this->propertyTypeExtractor, $this->denormalizer);
    }

    /**
     * @test
     */
    public function shouldSuccessSupports(): void
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => AuthorizeActionInterface::class,
        ]);

        $supports = $this->converter->supports($configuration);

        self::assertTrue($supports);
    }

    /**
     * @test
     */
    public function shouldNotSupportsIfClassIsInvalid(): void
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => \stdClass::class,
        ]);

        $supports = $this->converter->supports($configuration);

        self::assertFalse($supports);
    }

    /**
     * @test
     */
    public function shouldNotSupportsIfConverterIsInvalid()
    {
        $configuration = new ParamConverter([
            'converter' => 'some',
            'class'     => AuthorizeActionInterface::class,
        ]);

        $supports = $this->converter->supports($configuration);

        self::assertFalse($supports);
    }

    /**
     * @test
     */
    public function shouldSuccessApplyWithoutProperties(): void
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => TestedAuthorizeAction::class,
            'name'      => 'myParameter',
        ]);

        $request = new Request();

        $this->converter->apply($request, $configuration);

        self::assertArrayHasKey('myParameter', $request->attributes->all());
        self::assertEquals(new TestedAuthorizeAction(), $request->attributes->get('myParameter'));
    }

    /**
     * @test
     */
    public function shouldSuccessApplyWithFields()
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => TestedAuthorizeAction::class,
            'name'      => 'myParameter',
            'options'   => [
                'fields' => [
                    'id' => '__id',
                ],
            ],
        ]);

        $this->propertyTypeExtractor->expects(self::once())
            ->method('getTypes')
            ->with(TestedAuthorizeAction::class, 'id')
            ->willReturn([new Type('int', false)]);

        $this->denormalizer->expects(self::never())
            ->method('denormalize');

        $request = new Request();
        $request->attributes->set('__id', 123);

        $this->converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('myParameter'));
        $result = $request->attributes->get('myParameter');

        $expectedResult = new TestedAuthorizeAction();
        $expectedResult->id = 123;

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @test
     *
     * @expectedException \FiveLab\Bundle\AuthorizeActionBundle\Exception\MissingAttributeException
     * @expectedExceptionMessage Missing the attribute with name "__id" in request.
     */
    public function shouldFailIfNotNullableFieldIsMissing()
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => TestedAuthorizeAction::class,
            'name'      => 'myParameter',
            'options'   => [
                'fields' => [
                    'id' => '__id',
                ],
            ],
        ]);

        $this->propertyTypeExtractor->expects(self::once())
            ->method('getTypes')
            ->with(TestedAuthorizeAction::class, 'id')
            ->willReturn([new Type('int', false)]);

        $this->denormalizer->expects(self::never())
            ->method('denormalize');

        $request = new Request();

        $this->converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('myParameter'));
        $result = $request->attributes->get('myParameter');

        $expectedResult = new TestedAuthorizeAction();
        $expectedResult->id = 123;

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function shouldSuccessIfNullableFieldIsMissing()
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => TestedAuthorizeAction::class,
            'name'      => 'myParameter',
            'options'   => [
                'fields' => [
                    'id' => '__id',
                ],
            ],
        ]);

        $this->propertyTypeExtractor->expects(self::once())
            ->method('getTypes')
            ->with(TestedAuthorizeAction::class, 'id')
            ->willReturn([new Type('int', true)]);

        $this->denormalizer->expects(self::never())
            ->method('denormalize');

        $request = new Request();

        $this->converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('myParameter'));
        $result = $request->attributes->get('myParameter');

        self::assertEquals(new TestedAuthorizeAction(), $result);
    }

    /**
     * @test
     */
    public function shouldSuccessWithDenormalization()
    {
        $configuration = new ParamConverter([
            'converter' => 'authorize_action',
            'class'     => TestedAuthorizeAction::class,
            'name'      => 'myParameter',
            'options'   => [
                'fields' => [
                    'relation' => '__relation',
                ],
            ],
        ]);

        $this->propertyTypeExtractor->expects(self::once())
            ->method('getTypes')
            ->with(TestedAuthorizeAction::class, 'relation')
            ->willReturn([new Type('object', false, \stdClass::class)]);

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with((object) ['field' => 'value'], \stdClass::class)
            ->willReturn((object) ['field' => 'denormalized']);

        $request = new Request();
        $request->attributes->set('__relation', (object) ['field' => 'value']);

        $this->converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('myParameter'));
        $result = $request->attributes->get('myParameter');

        $expectedResult = new TestedAuthorizeAction();
        $expectedResult->relation = (object) ['field' => 'denormalized'];

        self::assertEquals($expectedResult, $result);
    }
}
