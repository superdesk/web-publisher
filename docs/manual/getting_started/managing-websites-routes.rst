Managing websites and routes
===================================

When Publisher is installed, it is integrated into Superdesk and expects output channels (or, in other words - websites) to be set - as already mentioned in :doc:`Configuring Publisher </manual/getting_started/publisher-configuration>`.

The main concepts are: 

- there can be more than one website that you can configure and feed from Superdesk
- each website is configured firstly by its **routes**; routes can be of type *collection*, *content* and *custom*.

.. image:: websites-routes-00.png
   :alt: Collection route
   :align: center

Route of type *collection* is expected to get articles attached to it - think of it as a kind of category page (Business, or Politics, or simply News). When configuring this route, you need to also specify an article template name - the one that will be used to show articles attached to that route.

.. image:: websites-routes-02.png
   :alt: Content route
   :align: center

Route of type *content* is the destination - it holds the content! Either only one article is attached to it, or no articles at all! So it can be either a static article page (About us), or a special template (contact form, confirmation page, or simply a route that doesn't directly hold attached articles, like 'home' route for example, or Trending articles, which would use a special template to show that kind of content).

.. image:: websites-routes-03.png
   :alt: Custom route
   :align: center

Routes of type *custom* are dynamically generated and thus have more technical aspect in essence. They are used to define, for example, author profile routes - /author/john-smith, /author/sarrah-staffwriter etc. The first part is static ('author'), while the second part is dynamic and based on an author slug in this case. Written in json format, a custom route definition looks like this:

.. code-block:: JSON
   {
      "name": "Author",
      "slug": "author",
      "type": "custom",
      "requirements": [
         {
            "key": "authorSlug",
            "value": "[0-9a-zA-Z\\-_]+"
         }
      ],
      "variable_pattern": "/{authorSlug}",
      "template_name": "author.html.twig"
   }

Here a theme developer would needs to get the author slug value from the context and construct the url dynamically using it.  
