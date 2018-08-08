Handling Article Slideshows
===========================

Listing a Single Article's Slideshow
------------------------------------

Usage:

.. code-block:: twig

    {% gimme slideshow with { name: "slideshow1" } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        {{ slideshow.items }} <!-- Slideshow items (article media) -->
        {% foreach media in items %}
            <img src="{{ url(media) }}" />
        {% endfor %}
        {{ slideshow.createdAt }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt }} <!-- Slideshow's updated at datetime-->
    {% endgimme %}

or

    {% gimme slideshow with { name: "slideshow1", article: gimme.article } %}
        {{ slideshow.code }} <!-- Slideshow's code -->
        {{ slideshow.items }} <!-- Slideshow items (article media) -->
        {% foreach media in items %}
            <img src="{{ url(media) }}" />
        {% endfor %}
        {{ slideshow.createdAt }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt }} <!-- Slideshow's updated at datetime-->
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
        {{ slideshow.items }} <!-- Slideshow items (article media) -->
        {% foreach media in items %}
            <img src="{{ url(media) }}" />
        {% endfor %}
        {{ slideshow.createdAt }} <!-- Slideshow's created at datetime -->
        {{ slideshow.updatedAt }} <!-- Slideshow's updated at datetime-->
    {% endgimme %}

The above twig code will render the list of articles slideshows for the current article set in context.
