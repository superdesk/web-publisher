gimmelist
`````````

The :code:`gimmelist` tag has two required parameters and two optional parameters:

 * (required) Name of variable available inside block: :code:`article`
 * (required) Keyword :code:`from` and type of requested Metas in collection: :code:`from articles` with filters passed to Meta Loader as extra parameters (:code:`start`, :code:`limit`, :code:`order`)
 * (optional) Keyword :code:`with` and parameters for Meta Loader, for example: :code:`with {foo: 'bar', param1: 'value1'}`
 * (optional) Keyword :code:`without` and parameters for Meta Loader, for example: :code:`without {source: 'AAP'}`
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

Example with filtering articles by metadata:

.. code-block:: twig

    {% gimmelist article from articles with {metadata: {byline: "Karen Ruhiger", located: "Sydney"}} %}
        {{ article.title }}
    {% endgimmelist %}

The above example will list all articles by metadata which contain ``byline`` equals to ``Karen Ruhiger`` **AND** ``located`` equals to ``Sydney``.

To list articles by authors you can also do:

.. code-block:: twig

    {% gimmelist article from articles with {author: ["Karen Ruhiger", "Doe"]} %}
        {{ article.title }}
        Author(s): {% for author in article.authors %}<img src="{{ author.avatarUrl }}" />{{ author.name }} ({{ author.role }}) {{ author.biography }} - {{ author.jobTitle.name }},{% endfor %}
    {% endgimmelist %}

It will then list all articles written by ``Karen Ruhiger`` **AND** ``Doe``.

To list articles from the ``Forbes`` source but without an ``AAP`` source you can also do:

.. code-block:: twig

    {% gimmelist article from articles with {source: ["Forbes"]} without {source: ["AAP"]} %}
        {% for source in article.sources %} {{ source.name }} {% endfor %}
    {% endgimmelist %}

It will then list all articles **with** source ``Forbes`` and **without** ``AAP``.


Example with usage of all parameters:

.. code-block:: twig

    {% gimmelist article from articles|start(0)|limit(10)|order('id', 'desc')
        with {foo: 'bar', param1: 'value1'}
        contextIgnore ['route', 'article']
        if article.title == "New Article 1"
    %}
        {{ article.title }}
    {% endgimmelist %}
