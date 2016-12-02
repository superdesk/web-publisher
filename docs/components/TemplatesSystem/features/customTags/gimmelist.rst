gimmelist
`````````

The :code:`gimmelist` tag has two required parameters and two optional parameters:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keyword :code:`from` and type of requested Metas in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader, for example: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keyword :code:`if` and expression used for results filtering

Here is an example of the required parameters:

.. code-block:: twig

    {% gimmelist article from articles %}
        {{ article.title }}
    {% endgimmelist %}

An here's an example using all parameters:

.. code-block:: twig

    {% gimmelist article from articles|start(0)|limit(10)|order('id', 'desc')
        with {foo: 'bar', param1: 'value1'}
        if article.title == "New Article 1"
    %}
        {{ article.title }}
    {% endgimmelist %}
