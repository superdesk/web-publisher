Handling Article Authors
========================

Listing a Single Article's Author
---------------------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimme author with { id: 1 } %}
        <li>{{ author.name }}</li> <!-- Author's name -->
        <li>{{ author.role }}</li> <!-- Author's name -->
        <li>{{ author.biography }}</li> <!-- Author's biography -->
        <li>{{ author.jobTitle.name }}</li> <!-- Author's job title name -->
        <li>{{ author.jobTitle.qcode }}</li> <!-- Author's job title code -->
    {% endgimme %}
    </ul>

Parameters:

.. code-block:: twig

    {% gimme author with { id: 1 } %} - select author by it's id.


.. code-block:: twig

    {% gimme author with { name: "Tom" } %} - select author by it's name.


Listing a collection of Article's Authors
-----------------------------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimmelist author from authors with { role: ["writer"] } without {role: ["subeditor"]} %}
        <li>{{ author.name }}</li> <!-- Author's name -->
        <li>{{ author.role }}</li> <!-- Author's name -->
        <li>{{ author.biography }}</li> <!-- Author's biography -->
        <li>{{ author.jobTitle.name }}</li> <!-- Author's job title name -->
        <li>{{ author.jobTitle.qcode }}</li> <!-- Author's job title code -->
        <li><img src="{{ author.avatarUrl }}"><li> <!-- Author's job title code -->
    {% endgimmelist %}
    </ul>

The above twig code, will render the list of articles where author's role is ``writer`` and is not ``subeditor``.

Filter authors by author's name:

.. code-block:: twig

    {% gimmelist author from authors with { name: ["Tom"] } %}

Filter authors by author's name and role:

.. code-block:: twig

    {% gimmelist author from authors with { role: ["Writer"], name: ["Tom"] } %}


Filter authors by job title:

.. code-block:: twig

    {% gimmelist author from authors with {jobtitle: {name: "quality check"}} %}
        {{ author.name }}
    {% endgimmelist %}

    {% gimmelist author from authors with {jobtitle: {qcode: "123"}} %}
        {{ author.name }}
    {% endgimmelist %}
