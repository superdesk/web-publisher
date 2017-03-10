Loading Meta from template
==========================

Meta Loaders
------------

:code:`Meta Loader` provides easy way for fetching data directly from template file.
Loader can return single meta or collection of meta's (with :code:`MetaCollection` class).

Library provides :code:`ChainLoader` class witch can simplify work with many loaders.

:code:`Meta Loader` must implement `Loader Interface <https://github.com/SuperdeskWebPublisher/templates-system/blob/master/Gimme/Loader/LoaderInterface.php>`_.

How to load single Meta?
------------------------

.. include:: /components/TemplatesSystem/features/customTags/gimme.rst

How to load Meta collection?
----------------------------

.. include:: /components/TemplatesSystem/features/customTags/gimmelist.rst