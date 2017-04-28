Introduction
============

When is the Publisher the right choice?
---------------------------------------

- If your organization already uses Superdesk, then search no more, Publisher is built to work with Superdesk. Fullstop.
- If you believe that experience and knowledge gained from 17 years of delivering digital news at scale with Newscoop CMS make people behind Publisher qualified to conceptualize and develop completely new publishing solution from the scratch
- Technically speaking, if this sounds good to you: Publisher is an API-centric delivery tool for all digital platforms; is a lightweight PHP 7 renderer for HTTP pushed content in HTML/CSS/JavaScript templates and has no CMS back end of its own - content creation is managed within Superdesk (or other system that you might be already using), not the Publisher.
- Also, in case you wandered, Publisher supports multi-tenancy and is designed for software-as-a-service, no installation or maintenance is required by the news organisation. Anyhow, Publisher runs on a standard web server, or in a Docker container, for test or production deployment.
- If you want to have presentation of articles taken care of by a flexible, device-responsive themes system which can be customised to match your publication - or built from scratch as well.
- If you believe in open source, we have you covered: The Superdesk Publisher code is open source, released on GitHub (https://github.com/superdesk/web-publisher) under the GNU Affero General Public License version 3.
- If you want to work with the Symfony stack


What is the Publisher’s focus?
------------------------------

- Exclusive focus on the specific features needed to deliver content effectively to multiple digital channels.
- Rapid development of new web site and digital layouts, independently from back-end systems to avoid disrupting editorial workflows.
- All of the latest embeds and custom widgets for your web pages.
- Integrates seamlessly with Superdesk, allowing monitoring of content lifecycles from production to delivery. Overview of content performance in real time.
- Full commercial support from the upstream development and implementation teams.

How the Publisher is structured?
--------------------------------

This graphic shows how the Publisher is configured.

.. image:: publisher-architecture.png
    :align: center
    :alt: Publisher Architecture

- Data: Symfony uses Postgres and Doctrine.
- Basic Frameworks: Publisher is based on Symfony and the Symfony CMF.
- Publisher: The rest is Publisher

Which components are packed into the Publisher?
-----------------------------------------------

- Multitenancy
- User management
- Built-in support for Reverse Proxy Caching (Varnish, Nginx, internal system as a fallback)
- Live-site management
- Content A/B/C testing support
- Widgets support (managed by editors)

Website architecture
--------------------

A basic theme must have the following structure:

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
