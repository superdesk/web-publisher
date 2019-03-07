Handling Related Articles
=========================

Listing a collection of Related Articles
----------------------------------------

Usage:

.. code-block:: twig

    <ul>
    {% gimmelist relatedArticle from relatedArticles with { article: gimme.article }} %}
        <li>{{ relatedArticle.article.title }}</li> <!-- Related article's title -->
        <a href="{{ url(relatedArticle.article) }}">Link</a> <!-- Related article's URL -->
        <li>{{ relatedArticle.createdAt|date('Y-m-d') }}</li> <!-- Related article's creation date -->
        <li>{{ relatedArticle.updatedAt|date('Y-m-d') }}</li> <!-- Related article's update date -->
    {% endgimmelist %}
    </ul>

The above twig code, will render the list of related articles by given article in ``with`` parameters.

The ``{{ relatedArticle.article }}`` object is an :doc:`article object </manual/templates_system/articles>`.

Specifying ``article`` parameter is optional. By default the article from context will be loaded.
