Usage
=====

Webhooks Management
-------------------

Bundle provides abstract Controller class for CRUD webhook actions. It cane be used in Your own controllers.

Sending Webhooks
----------------

Bundle allows to create new webhooks, and provides repository for searching them by event name.
Sending events need to be implemented in end application. We recommend to do that with dispatcher and event
listeners/subscribers.

Example implementation can be found in ``SWP\Bundle\CoreBundle\EventSubscriber\WebhookEventsSubscriber`` class (in
Superdesk Publisher project).


