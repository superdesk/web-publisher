Handling Routes
===============

Listing a Single Route
---------------------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimme route with { slug: 'test-route', name: 'Test Route'} %}
        <li>{{ route.name }}</li> <!-- Route's name -->
        <li>{{ route.slug }}</li> <!-- Route's slug -->
        <li>{{ url(route) }}</li> <!-- Route's url -->
    {% endgimme %}
    </ul>
