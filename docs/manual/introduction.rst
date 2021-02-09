Introduction
============

Why choose Publisher?
---------------------

If your organisation already creates and produces content in Superdesk, Publisher is built to work with it natively. If you are not using Superdesk, but your back-end system or systems are still fit for purpose and your need is to manage a portfolio of digital assets (from multiple websites to apps to social feeds), Superdesk Publisher can be integrated with your legacy tools until their deprecation and replacement.


What is Publisher’s focus?
--------------------------

- Efficient content delivery to multiple digital channels.
- Rapid development of new website and digital layouts, independently from back-end systems to avoid disrupting editorial workflows.
- All the latest embeds and custom widgets for your web pages.
- Full commercial support from the upstream development and implementation teams.


How is Publisher structured?
----------------------------

This graphic shows how Publisher is configured.

.. image:: publisher-architecture.png
    :align: center
    :alt: Publisher Architecture

- Data: Symfony uses Postgres and Doctrine.
- Basic frameworks: Publisher is based on Symfony and the Symfony CMF.
- Publisher: The rest is Publisher


Notable features
----------------

- Multitenancy
- Built-in support for reverse proxy caching (Varnish, Nginx, internal system as a fallback)
- Twig or PWA theme support


Website architecture
--------------------

A basic Twig theme must have the following structure:

.. code-block:: bash

    ExampleTheme/               <=== Theme starts here
        views/                  <=== Views directory
            article.html.twig
            base.html.twig
            category.html.twig
            home.html.twig
        translations/           <=== Translations directory
            messages.en.xlf
            messages.de.xlf
        public/                 <=== Assets directory
            css/
            js/
            images/
        theme.json              <=== Theme configuration

More on *themes* in chapter :doc:`Themes </manual/themes/index>`
