Models
======

Subscription
------------

Every subscription is represented by a **Subscription** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique identifier                         |
+--------------+-------------------------------------------+
| type         | Subscription's type (``recurring`` etc.)  |
+--------------+-------------------------------------------+
| details      | Subscription details                      |
+--------------+-------------------------------------------+
| code         | Subscription's unique code                |
+--------------+-------------------------------------------+
| active       | Subscription's status                     |
+--------------+-------------------------------------------+
| updatedAt    | Subscription updated at datetime          |
+--------------+-------------------------------------------+
| createdAt    | Subscription created at datetime          |
+--------------+-------------------------------------------+

.. note::

    This model implements ``SWP\Component\Paywall\Model\SubscriptionInterface``.
