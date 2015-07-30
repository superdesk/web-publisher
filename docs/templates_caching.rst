Templates Caching
=================

For now we support just themplate blocks caching with :code:`cache` block.

:code:`Cache` block is simple, and accepts only two parameters: cache key and strategy object (with strategy key and value).

.. note::

    Cache blocks can be nested:

    .. code-block:: twig

        {% cache 'v1' 900 %}
            {% for item in items %}
                {% cache 'v1' item %}
                    {# ... #}
                {% endcache %}
            {% endfor %}
        {% endcache %}

    The annotation can also be an expression:

    .. code-block:: twig

        {% set version = 42 %}
        {% cache 'hello_v' ~ version 900 %}
            Hello {{ name }}!
        {% endcache %}

There is no need to invalidate keys - system will clear not used cache entries automaticaly. 

Strategies
``````````

There are two available cache strategies: :code:`lifetime` and :code:`generational`.

With :code:`lifetime` as a strategy key you need provide :code:`time` with value in seconds.

.. code-block:: twig

    {# delegate to lifetime strategy #}
    {% cache 'v1/summary' {time: 300} %}
        {# heavy lifting template stuff here, include/render other partials etc #}
    {% endcache %}

With :code:`generational` as a strategy key you need provide :code:`gen` with object or array as value.

.. code-block:: twig

    {# delegate to generational strategy #}
    {% cache 'v1/summary' {gen: item} %}
        {# heavy lifting template stuff here, include/render other partials etc #}
    {% endcache %}
