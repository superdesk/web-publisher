gimme
`````

The tag :code:`gimme` has one required parameter and one optional parameter:

 * (required) Meta object type (and name of variable available inside block), for example: *article*
 * (optional) Keword :code:`with` and parameters for Meta Loader, for example: :code:`{ param: "value" }`

.. code-block:: twig

    {% gimme article %}
        {# article Meta will be available under "article" variable inside block #}
        {{ article.title }}
    {% endgimme %}

Meta Loaders sometimes require special parameters - like the article number, language of the article, user id, etc..

.. code-block:: twig

    {% gimme article with { articleNumber: 1 } %}
        {# Meta Loader will use provided parameters to load article Meta #}
        {{ article.title }}
    {% endgimme %}