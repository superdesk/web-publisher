Interfaces
==========

Subscriber Interface
--------------------

This component contains ``SWP\Component\Paywall\Model\SubscriberInterface`` interface
which should be implemented by your user class.

Paywall Secured Interface
-------------------------

This component contains ``SWP\Component\Paywall\Model\PaywallSecuredInterface`` interface
which should be implemented by classes that must be flagged as "paywall secured".


Paywall Secured Trait
---------------------

This component contains ``SWP\Component\Paywall\Model\PaywallSecuredTrait`` trait
which adds a special property along with getter and setter. By using this trait it is possible to
flag your objects as paywall secured.
