<?php
/**
 * Created by PhpStorm.
 * User: pawelmikolajczuk
 * Date: 09.03.2017
 * Time: 09:18.
 */

namespace SWP\Bridge\JMSSerializerBundle\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use Metadata\MetadataFactory;
use PhpCollection\Map;
use SWP\Bridge\JMSSerializerBundle\JMSSerializer;

class JMSSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializer()
    {
        $factory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));

        $handlerRegistry = new HandlerRegistry();
        $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        $objectConstructor = new UnserializeObjectConstructor();
        $serializationVisitors = new Map(array(
            'json' => new JsonSerializationVisitor($namingStrategy),
        ));
        $deserializationVisitors = new Map(array(
            'json' => new JsonDeserializationVisitor($namingStrategy),
        ));

        $serializer = new JMSSerializer(new Serializer(
            $factory,
            $handlerRegistry,
            $objectConstructor,
            $serializationVisitors,
            $deserializationVisitors
        ));

        self::assertEquals('{"a":"b"}', $serializer->serialize(['a' => 'b'], 'json'));
        self::assertEquals(['a' => 'b'], $serializer->deserialize('{"a":"b"}', 'array', 'json'));
    }
}
