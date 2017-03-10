Superdesk Web Publisher technical documentation
===============================================

*The next generation publishing platform for journalists and newsrooms*

Superdesk Web Publisher is a lightweight open source renderer which takes news articles from an API feed and delivers them directly to readers via the Internet. The code is released under the `GNU Affero General Public License, version 3 <https://github.com/superdesk/web-publisher/blob/master/LICENSE.md>`_.

The Web Publisher is designed to work with the `Superdesk <https://www.superdesk.org/>`_ news room management system from `Sourcefabric <https://www.sourcefabric.org/>`_, but could be made to work with any compatible API. It is written in PHP and runs on a standard web server or in a Docker container. A PostgreSQL database is also required.

The presentation of articles is taken care of by a flexible, device-responsive themes system which can be customised to match your publication.

This documentation includes text and code examples from the Symfony and Sylius projects, released under the `Creative Commons BY-SA 3.0 <http://creativecommons.org/licenses/by-sa/3.0/>`_ license.
Pull requests to `improve the documentation <http://superdesk-web-publisher.readthedocs.io/en/latest/contributing/documentation/index.html>`_ are very welcome.

.. toctree::
   :caption: Documentation for theme designers
   :maxdepth: 2

   themes
   templates_system/index

.. toctree::
   :caption: Documentation for developers
   :maxdepth: 2

   bundles/index
   components/index
   content_publishing

.. toctree::
   :caption: API documentation
   :maxdepth: 2

   internal_api/index

.. toctree::
   :caption: Cookbooks
   :maxdepth: 2

   cookbooks/index

Contributing to Web Publisher
-----------------------------

.. toctree::
   :hidden:

   contributing/index

.. include:: /contributing/map.rst.inc
