Handling Article Media
======================

Listing Article Media
---------------------

Publisher have concept of Meta Loaders - one of built in loaders covers article media.

Article Media
`````````````

The :code:`articleMedia` loader have one optional parameter:

 * (optional) key :code:`article` - article Meta instance used for loading meta (if omitted then one available in context is used).

Simple usage:

.. code-block:: twig

    {% gimmelist media from articleMedia %} <!-- It will use article from context -->
        <img src="{{ url(media) }}" />
    {% endgimmelist %}

With optional parameter:

.. code-block:: twig

    {% gimmelist media from articleMedia with {'article': gimme.article} %}
        <img src="{{ url(media) }}" />
    {% endgimmelist %}

.. note::

    Media Meta is handled by default by :code:`url` and :code:`uri` functions. It will return url for original image or file.

Image Renditions
````````````````

If provided article media is an Image then it can have custom renditions. You can loop through renditions and display them.

Usage:

.. code-block:: twig

    {% gimmelist media from articleMedia with {'article': gimme.article} %}
        {% if media.renditions is iterable %}
            {% for rendition in media.renditions %}
                <img src="{{ url(rendition) }}" style="width: {{ rendition.width }}px; height: {{ rendition.height }}px;" />
            {% endfor %}
        {% endif %}
    {% endgimmelist %}

Get selected rendition only:


.. code-block:: twig

    {% gimmelist media from articleMedia with {'article': gimme.article} %}
        {% gimme rendition with { 'name': '16-9', 'fallback': 'original' } %}
            <img src="{{ url(rendition) }}" style="width: {{ rendition.width }}px; height: {{ rendition.height }}px;" />
        {% endgimme %}
    {% endgimmelist %}

.. note::

    'original' is default feedback value for single rendition loader.

Feature Media
`````````````

If Item comes with :code:`featuremedia` association then Article will have this media set as :code:`featureMedia`.

Usage:

.. code-block:: twig

    {% if gimme.article.featureMedia.renditions is iterable %}
        {% for rendition in gimme.article.featureMedia.renditions %}
            <img src="{{ url(rendition) }}" style="width: {{ rendition.width }}px; height: {{ rendition.height }}px;" />
        {% endfor %}
    {% endif %}

Or get selected rendition:

.. code-block:: twig

    {% gimme rendition with { 'media': gimme.article.featureMedia, 'name': '16-9', 'fallback': 'original' } %}
        <img src="{{ url(rendition) }}" style="width: {{ rendition.width }}px; height: {{ rendition.height }}px;" />
    {% endgimme %}
