<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\Common\Serializer\SerializerInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class TenantHandler implements SubscribingHandlerInterface
{
    private $tenantRepository;
    private $serializer;

    public function __construct(TenantRepositoryInterface $tenantRepository, SerializerInterface $serializer)
    {
        $this->tenantRepository = $tenantRepository;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => TenantInterface::class,
                'method' => 'serializeToJson',
            ),
        );
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        string $tenantCode
    ) {
        $tenant = $this->tenantRepository->findOneByCode($tenantCode);

        if (null === $tenant) {
            return [];
        }

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        return $serializer->toArray($tenant);
    }
}
