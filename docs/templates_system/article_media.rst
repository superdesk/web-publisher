Handling Article Media
======================

Listing Article Media
---------------------

Web Publisher have concept of Meta Loaders - one of built in loaders covers article media.

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

