Usage
=====

What is Rule Evaluator?
~~~~~~~~~~~~~~~~~~~~~~~

Rule evaluators are used to evaluate rule on an given object and make sure it matches the rule's criteria.
By default, the `Symfony Expression Language Component`_ is used which perfectly fits into
the business rules engine concept.

Adding new Rule Evaluator
~~~~~~~~~~~~~~~~~~~~~~~~~

There is a possibility to create your custom implementation for Rule Evaluator. All you need to do is to create
a new class and implement ``SWP\Component\Rule\Evaluator\RuleEvaluatorInterface`` interface.

What is Rule Processor?
~~~~~~~~~~~~~~~~~~~~~~~

Rule processor is responsible for processing all rules and apply them respectively to an object,
based on the defined rule priority. The greater the priority value, the higher the priority.

Rule Processor implements ``SWP\Component\Rule\Processor\RuleProcessorInterface`` interface.

What is Rule Applicator?
~~~~~~~~~~~~~~~~~~~~~~~~

Rule applicators are used to apply given rule's configuration to an object. You create your custom rule applicators
and register them in Rule Applicator Chain service which triggers ``apply`` method on them if the given applicator
is supported by the rule subject.

Adding new Rule Applicator
~~~~~~~~~~~~~~~~~~~~~~~~~~

There is a possibility to create your custom implementation for Rule Applicator. All you need to do is to create
a new class and implement ``SWP\Component\Rule\Applicator\RuleApplicatorInterface`` interface.

.. _Symfony Expression Language Component: http://symfony.com/doc/current/components/expression_language.html
