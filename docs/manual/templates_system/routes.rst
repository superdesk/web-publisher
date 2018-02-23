Handling Routes
===============

Listing a Single Route
----------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimme route with { parent: 5, slug: 'test-route', name: 'Test Route'} %}
        <li>{{ route.name }}</li> <!-- Route's name -->
        <li>{{ route.slug }}</li> <!-- Route's slug -->
        <li>{{ url(route) }}</li> <!-- Route's url -->
    {% endgimme %}
    </ul>

- ``parent`` - an id of parent route
- ``slug`` - route's slug
- ``name`` - route's name
