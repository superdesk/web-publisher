Keywords
========

List a Single Keyword
---------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimme keyword with { slug: 'big-city' } %}
        <li>{{ keyword.name }}</li> <!-- Keyword's name -->
        <li>{{ keyword.slug }}</li> <!-- Keyword's slug -->
    {% endgimme %}
    </ul>
