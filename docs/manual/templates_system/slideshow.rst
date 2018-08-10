Handling Article Slideshows
===========================

Listing a Single Article's Slideshow
------------------------------------

Usage:

.. code-block:: twig

    {% gimme slideshow with { name: "slideshow1" } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        {{ slideshow.createdAt|date('Y-m-d hh:mm') }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt|date('Y-m-d hh:mm') }} <!-- Slideshow's updated at datetime-->
    {% endgimme %}

or

.. code-block:: twig

    {% gimme slideshow with { name: "slideshow1", article: gimme.article } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        {{ slideshow.createdAt|date('Y-m-d hh:mm') }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt|date('Y-m-d hh:mm') }} <!-- Slideshow's updated at datetime-->
    {% endgimme %}

Parameters:

.. code-block:: twig

    {% gimme slideshow with { name: "slideshow1", article: gimme.article } %} {{ slideshow.code }} {% endgimme %} - select slideshow by it's code/name and current article.

If the ``article`` parameter is not provided, the slideshow will be loaded for the current article that is set in the context.

Listing a collection of Article's Slideshows
--------------------------------------------

Usage:

.. code-block:: twig

    {% gimmelist slideshow from slideshows with { article: gimme.article } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        {{ slideshow.createdAt|date('Y-m-d hh:mm') }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt|date('Y-m-d hh:mm') }} <!-- Slideshow's updated at datetime-->
    {% endgimmelist %}

The above twig code will render the list of articles slideshows for the current article set in context.


Listing all Article's Slideshows Items
--------------------------------------

Usage:

.. code-block:: twig

    {% gimmelist slideshowItem from slideshowItems with { article: gimme.article } %}
        {% gimme rendition with {'media': slideshowItem.articleMedia, 'name': '770x515', 'fallback': 'original' } %}
            <img src="{{ url(rendition) }}" />
        {% endgimme %}
    {% endgimmelist %}

The above twig code will render the list of articles slideshows for the current article set in context.

Listing all Article's Slideshows and its Items
----------------------------------------------

Usage:

.. code-block:: twig

    {% gimmelist slideshow from slideshows with { article: gimme.article } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        <!-- Slideshow items -->
        {% gimmelist slideshowItem from slideshowItems with { article: gimme.article, slideshow: slideshow } %}
            {% gimme rendition with {'media': slideshowItem.articleMedia, 'name': '770x515', 'fallback': 'original' } %}
                <img src="{{ url(rendition) }}" />
            {% endgimme %}
        {% endgimmelist %}
        {{ slideshow.createdAt|date('Y-m-d hh:mm') }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt|date('Y-m-d hh:mm') }} <!-- Slideshow's updated at datetime-->
    {% endgimmelist %}

The ``article`` parameter in ``gimmelist`` is optional. If not provided, it will load slideshows for current article.
