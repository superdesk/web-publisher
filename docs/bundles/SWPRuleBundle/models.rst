Models
======

Rule
----

Rule is to check if your "rule aware" objects are allowed to be processed and if some rule's configuration can be applied to it.

A rule is configured using the ``configuration`` attribute which is an array serialized into database.
Your custom Rule Applicator should define which configuration key-value pair should be applied. For example, you could configure the `route` key to define which route should be applied to an object if the
given rule evaluates to true. You could also apply ``templateName`` or any other keys.

See :doc:`/bundles/SWPRuleBundle/usage` section for more details.


RuleSubjectInterface
--------------------

To make use of the Rule bundle and allow to apply rule to an object, the entity must be "rule aware",
it means that "subject" class needs to implement ``SWP\Component\Rule\Model\RuleSubjectInterface`` interface.

If you make your custom entity rule aware, the Rule Processor will automatically process all rules for given object.

By implementing ``SWP\Component\Rule\Model\RuleSubjectInterface`` interface, your object will have to define the following method:

- ``getSubjectType()`` - should return the name of current object, for example: ``article``.

Rule Evaluator is using this method to evaluate rule on an object.

If the ``getSubjectType()`` returns ``article`` the rule
expression should be related to this object using ``article`` prefix. For example: ``article.getSomething('something') > 1``.
