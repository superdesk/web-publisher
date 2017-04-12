<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Rule\Applicator;

use Psr\Log\LoggerInterface;
use SWP\Bundle\CoreBundle\Factory\PublishActionFactoryInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Rule\PublishDestinationResolverInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Component\Rule\Applicator\RuleApplicatorInterface;
use SWP\Component\Rule\Model\RuleInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PackageRuleApplicator implements RuleApplicatorInterface
{
    /**
     * @var ArticlePublisherInterface
     */
    private $articlePublisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PublishDestinationResolverInterface
     */
    private $publishDestinationResolver;

    /**
     * @var PublishActionFactoryInterface
     */
    private $publishActionFactory;

    /**
     * @var array
     */
    private $supportedKeys = ['destinations'];

    /**
     * PackageRuleApplicator constructor.
     *
     * @param ArticlePublisherInterface           $articlePublisher
     * @param PublishDestinationResolverInterface $publishDestinationResolver
     * @param PublishActionFactoryInterface       $publishActionFactory
     */
    public function __construct(
        ArticlePublisherInterface $articlePublisher,
        PublishDestinationResolverInterface $publishDestinationResolver,
        PublishActionFactoryInterface $publishActionFactory
    ) {
        $this->articlePublisher = $articlePublisher;
        $this->publishDestinationResolver = $publishDestinationResolver;
        $this->publishActionFactory = $publishActionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(RuleInterface $rule, RuleSubjectInterface $subject)
    {
        $configuration = $this->validateRuleConfiguration(json_decode($rule->getConfiguration(), true));

        if ($subject instanceof PackageInterface && !empty($configuration)) {
            foreach ($configuration[$this->supportedKeys[0]] as $value) {
                if (empty($this->validateDestinationConfig($value))) {
                    return;
                }
            }

            $destinations = [];
            foreach ($configuration[$this->supportedKeys[0]] as $destination) {
                $destinations[] = $this->publishDestinationResolver->resolve(
                    $destination['tenant'],
                    $destination['route']
                );
            }

            $publishAction = $this->publishActionFactory->createWithDestinations($destinations);

            $this->articlePublisher->publish($subject, $publishAction);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(RuleSubjectInterface $subject)
    {
        if (!$subject instanceof PackageInterface && 'package' === $subject->getSubjectType()) {
            $this->logger->warning(sprintf(
                '"%s" is not supported by "%s" rule applicator!',
                is_object($subject) ? get_class($subject) : gettype($subject),
                get_class($this)
            ));

            return false;
        }

        return true;
    }

    private function validateDestinationConfig(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('tenant');
        $resolver->setDefined('route');

        return $this->resolveConfig($resolver, $config);
    }

    private function validateRuleConfiguration(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined($this->supportedKeys[0]);

        return $this->resolveConfig($resolver, $config);
    }

    // TODO move to abstract rule applicator
    private function resolveConfig(OptionsResolver $resolver, array $config)
    {
        try {
            return $resolver->resolve($config);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return [];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
