Models
======

OutputChannel
-------------

Every output channel is represented by a **OutputChannel** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique identifier                         |
+--------------+-------------------------------------------+
| type         | Output channel's type (``wordpress`` etc.)|
+--------------+-------------------------------------------+
| config       | Output channel configuration per type     |
+--------------+-------------------------------------------+

.. note::

    This model implements ``SWP\Component\OutputChannel\Model\OutputChannelInterface``.

Output Channel Aware Interface
==============================

This component contains ``SWP\Component\OutputChannel\Model\OutputChannelAwareInterface`` interface
to make your custom entity/model output channel aware.
