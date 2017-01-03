gimmelist
`````````

The :code:`gimmelist` tag has two required parameters and two optional parameters:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keyword :code:`from` and type of requested Metas in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader, for example: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keyword :code:`if` and expression used for results filtering
 * (optional) Keyword :code:`ignoreContext` and optional array of selected meta to be ignored

Example of the required parameters:

.. code-block:: twig

    {% gimmelist article from articles %}
        {{ article.title }}
    {% endgimmelist %}

Example with ignoring selected context parameters:

.. code-block:: twig

    {% gimmelist article from articles ignoreContext ['route', 'article'] %}
    ...

Example with ignoring whole context

.. code-block:: twig

    {% gimmelist article from articles ignoreContext [] %}
    ...

Or even without empty array

.. code-block:: twig

    {% gimmelist article from articles ignoreContext %}
    ...

Example with usage of all parameters:

.. code-block:: twig

    {% gimmelist article from articles|start(0)|limit(10)|order('id', 'desc')
        with {foo: 'bar', param1: 'value1'}
        contextIgnore ['route', 'article']
        if article.title == "New Article 1"
    %}
        {{ article.title }}
    {% endgimmelist %}
