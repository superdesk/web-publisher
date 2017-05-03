Manual
======

**New Generation Publishing**

Modern online newsrooms require powerful publishing tools that can support their need to get stories out on time, 24/7 and delivered to any channel, platform or device. Successful digital publishers rely on the technology used to create content as much as on the content quality itself.

.. container:: image_bck

  .. image:: ../_static/manual.png
     :alt: example image
     :align: center

Open source PHP content management systems are very popular with news organisations and have evolved over a period of ten to fifteen years or more. Some examples are: Newscoop (1999), Drupal (2000), WordPress (2003), etc. The development of the Superdesk Publisher is based on almost two decades of experience that Sourcefabric gained in building Newscoop our web CMS. Much has been learned, and it’s time for a fresh approach.

*The motivation*

With Newscoop, we have done a lot of custom development work for each of our customers. We've focused on creating media websites that specifically tailored to individual requirements, both in terms of visual design and site functionality. Our customer’s websites are powered by Newscoop’s standard set of publishing and community tools, but most of the user-oriented functionality has been custom-built specifically for each project.

This approach worked when our focus was on improving our individual client sites, but has become inefficient due to the need to continuously iterate on each client’s individual platform.. Therefore, we decided to invest in making a new platform on which our customer’s projects could be more efficiently designed, built, launched, maintained and evolved.

With Superdesk Publisher we want to shift the creative focus of our design and media-development teams away from maintaining each client project individually and move in the direction of enabling numerous, highly-individualized client projects to be created and implemented without substantial effort and within a short time frame.
So, what is it?

The Superdesk Publisher is an API-centric delivery tool for all digital platforms. Written from scratch in 2016, it utilizes the knowledge gained from 17 years of delivering digital news at scale with Newscoop. The Publisher is designed to work with any editorial system. Naturally, it works the best with our in-house newsroom management system, Superdesk. Therefore, it allows independent maintenance, upgrade and change of the editorial back-end system.

The Superdesk Publisher is a lightweight PHP 7 renderer for HTTP pushed content in HTML/CSS/JavaScript templates and has no CMS back end of its own. Content creation is managed within Superdesk, not the Publisher.

We want to build an incredibly fast experience, not just for mobile but for desktop and other devices as well. Therefore, we designed the Superdesk Publisher to provide high page performance (read: how fast a page loads) via built-in support for Reverse Proxy Caching (Varnish, Nginx, internal system as a fallback).

The publisher will be a highly customisable tool with a set of design themes, device-specific templates, widgets and plugins that can scale for many different types and sizes of media clients and their multiple digital channels which can be contained within one single publishing system. Widgets such as advertising platforms, social media or sidebar content can be added or removed from ‘containers’ in the templates by site admins.
The Superdesk Publisher supports multi-tenancy and is designed for software-as-a-service, no installation or maintenance is required. Each instance can serve numerous websites with multiple themes and each tenant can have multiple themes. Such a setup makes management (updating instances, creating new tenants) fast and easy.

*For the newsroom*

Live-site management and content performance analytics are easy to use. The page editor and analytic tools are designed specifically for journalists and editors so that they can act fast and change the news order right when top news breaks.

With Content A/B/C testing support, journalists, editors and teams can easily test user experience across content and based on data results to better understand their readers, quickly make decisions and optimise the content.

By testing the content, editors can learn which articles (headlines, images, leads etc.) attract visitors the most and therefore, discover the most effective way to reduce bounce rates, increase the amount of time readers spend on their website, increase readership and improve social channels.

*For developers*

With a scalable Symfony2 application which serves as a REST APIs we have a maintainable and stable development environment.

Superdesk Publisher uses Twig for its templates. It is a flexible and pithy templating language, that allows templates to write the way you want. You don't have to know PHP or gain a knowledge on how databases work in order to get whatever content you need from the backend.

Superdesk Publisher runs on a standard web server, or in a Docker container, for test or production deployment.

The Superdesk Publisher code is open source, released on GitHub (https://github.com/superdesk/web-publisher) under the GNU Affero General Public License version 3.

.. toctree::
   :maxdepth: 2
   
   introduction
   getting_started/index
   templates_system/index
   themes/index
   editorial_tools/index
   admin_interface/index
