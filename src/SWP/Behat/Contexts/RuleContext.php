<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use function json_decode;
use SWP\Bundle\CoreBundle\Model\RuleInterface;
use SWP\Bundle\RuleBundle\Form\Type\RuleType;
use SWP\Component\Rule\Repository\RuleRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class RuleContext extends AbstractContext implements Context
{
    private $ruleRepository;

    private $ruleFactory;

    private $formFactory;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        FactoryInterface $ruleFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * @Given the following tenant publishing rule:
     */
    public function theFollowingTenantPublishingRule(PyStringNode $string)
    {
        $form = $this->submitForm($string);
        if ($form->isValid()) {
            $this->ruleRepository->add($form->getData());
            $this->ruleRepository->flush();
        } else {
            throw new \Exception('Rule configuration is invalid');
        }
    }

    /**
     * @Given the following organization publishing rule:
     */
    public function theFollowingOrganizationPublishingRule(PyStringNode $string)
    {
        $form = $this->submitForm($string);
        if ($form->isValid()) {
            /** @var RuleInterface $rule */
            $rule = $form->getData();
            $this->ruleRepository->add($rule);
            $rule->setTenantCode(null);
            $this->ruleRepository->flush();
        } else {
            throw new \Exception('Rule configuration is invalid');
        }
    }

    private function submitForm(PyStringNode $string): FormInterface
    {
        $form = $this->formFactory->create(RuleType::class, $this->ruleFactory->create());
        $form->submit(json_decode($string->getRaw(), true));

        return $form;
    }
}
