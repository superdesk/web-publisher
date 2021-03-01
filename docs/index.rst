Welcome to Superdesk Publisher documentation
============================================

*The next-generation publishing platform for newsrooms*

Superdesk Publisher is a lightweight open source renderer for news articles and other content delivered via an API feed. The code is released under the `GNU Affero General Public Licence, version 3 <https://github.com/superdesk/web-publisher/blob/master/LICENSE.md>`_.

Publisher is designed to work with the `Superdesk <https://www.superdesk.org/>`_ newsroom management system from `Sourcefabric <https://www.sourcefabric.org/>`_, but it can also be adapted to work with any compatible API. Publisher is a lightweight PHP 7 renderer for HTTP-pushed content in both HTML/CSS/JavaScript and PWA templates, and it runs on a standard web server or in a Docker container. A PostgreSQL database is also required.


.. container:: image_bck

  .. image:: _static/publisher.png
     :alt: example image
     :align: center

The presentation of articles is taken care of by a flexible, device-responsive themes system, which can be customised to suit your publications.

This documentation includes text and code examples from the Symfony and Sylius projects, released under the `Creative Commons BY-SA 3.0 <http://creativecommons.org/licenses/by-sa/3.0/>`_ licence. Pull requests to `improve the documentation <http://superdesk-publisher.readthedocs.io/en/latest/contributing/documentation/index.html>`_ are welcome.


.. toctree::
   :maxdepth: 3

   manual/index
   cookbooks/index
   reference/index
   bundles/index
   developers

   
Contributing to Web Publisher
-----------------------------

.. toctree::
   :hidden:

   contributing/index

.. include:: /contributing/map.rst.inc
