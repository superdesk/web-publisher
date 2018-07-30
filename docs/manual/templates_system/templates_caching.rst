Template Caching
================

For now we support just template block caching with the :code:`cache` block.

The :code:`Cache` block is simple, and accepts only two parameters: cache key and strategy object (with strategy key and value).

.. note::

    Cache blocks can be nested:

    .. code-block:: twig

        {% cache 'v1' {time: 900} %}
            {% for item in items %}
                {% cache 'v1' {gen: item} %}
                    {# ... #}
                {% endcache %}
            {% endfor %}
        {% endcache %}

    The annotation can also be an expression:

    .. code-block:: twig

        {% set version = 42 %}
        {% cache 'hello_v' ~ version {time: 300} %}
            Hello {{ name }}!
        {% endcache %}

There is no need to invalidate keys - the system will clear unused cache entries automatically. 

Strategies
``````````

There are two available cache strategies: :code:`lifetime` and :code:`generational`.

With :code:`lifetime` as a strategy key you need to provide :code:`time` with a value in seconds.

.. code-block:: twig

    {# delegate to lifetime strategy #}
    {% cache 'v1/summary' {time: 300} %}
        {# heavy lifting template stuff here, include/render other partials etc #}
    {% endcache %}

With :code:`generational` as a strategy key you need to provide :code:`gen` with object or array as the value.

.. code-block:: twig

    {# delegate to generational strategy #}
    {% cache 'v1/summary' {gen: gimme.article} %}
        {# heavy lifting template stuff here, include/render other partials etc #}
    {% endcache %}

.. note::

    You can pass Meta object to :code:`generational` strategy and it will be used for key generation.
    If Meta value have :code:`created_at` or :code:`updated_at` then those properties will be used, otherwise key will be generated only from object :code:`id`.


Content list blocks caching
```````````````````````````

It's important to always use :code:`generational` strategy for content lists (and it items) caching. Publisher will update cache key generated with it every time when
items on list are added/removed/reordered or when list criteria are updated or even when article used by list will be unpublished or updated.

.. code-block:: twig

    {% cache 'frontPageManualList' {gen: contentList} %}
        {# get and render list items here #}
    {% endcache %}
