Elements generated on theme installation
========================================

To provide better experience for end users after theme installation Publisher can create some default content used by theme.
Thanks to this feature theme developer can be sure that all of his article or category templates will be visible without
complicated configuration by end user.

Generated elements can be declared in theme.json config file under :code:`generatedData` key. Example:

.. code-block:: json

    {
     "name": "my/custom-theme",
     "generatedData": { ... }
    }

Theme generators supports now those elements: :code:`routes`, :code:`menus`, :code:`containers`, :code:`widgets`, and :code:`contentLists`.
All elements have this same properties as are supported by API requests, plus few extra like:
 * in routes: :code:`numberOfArticles` - number of fake articles generated and attached to route
 * in menus: :code:`children` - array of child menus attached to parent one
 * in widgets: :code:`containers` - array of containers names where widgets should be automatically attached


Example routes block
````````````````````

.. code-block:: json

    "generatedData": {
        "routes": [
            {
                "name": "Politics",                             # required
                "slug": "politics",                             # optional
                "type": "collection",                           # required
                "templateName": "category.html.twig",           # optional
                "articlesTemplateName": "article.html.twig",    # optional
                "numberOfArticles": 1                           # optional (number of articles generated and attached to route)
            },
        ...

Example menus block
```````````````````

.. code-block:: json

    "generatedData": {
        "menus": [
            {
                "name": "mainNavigation",               # required
                "label": "Main Navigation",             # optional
                "children": [                           # optional (array of child menus attached to parent one)
                    {
                        "name": "home",                 # required
                        "label": "Home",                # optional
                        "uri": "/"
                    }
                ]
            },
            {
                "name": "footerPrim",                   # required
                "label": "Footer Navigation",           # optional
                "children": [                           # optional (array of child menus attached to parent one)
                    {
                        "name": "politics",             # required
                        "label": "Politics",            # optional
                        "route": "Politics"             # optional (route name - can be one defined in this config)
                    },
        ...

Example containers block
````````````````````````

.. code-block:: json

    "generatedData": {
        "containers": [
            {
                "name": "mainNav",                      # required
                "styles": "border: 1px ",               # optional
                "visible": true,                        # optional (default true)
                "cssClass": "col-md-12",                # optional
                "data": {                               # optional
                    "some_extra_data": true
                }
            },
            {
                "name": "frontArticles"                 # required
            }
        ...

Example widgets block
`````````````````````

.. code-block:: json

    "generatedData": {
        "widgets": [
            {
                "name": "NavigationMain",               # required
                "type": "SWP\\Bundle\\TemplatesSystemBundle\\Widget\\MenuWidgetHandler", # required
                "visible": true,                        # required
                "parameters": {                         # optional
                    "menu_name": "mainNavigation",
                    "template_name": "menu1.html.twig"
                },
                "containers": ["mainNav"]               # optional
            },
        ...

Example contentLists block
``````````````````````````

.. code-block:: json

    "generatedData": {
        "contentLists": [
            {
                "name": "Example automatic list",                       # required
                "type": "automatic",                                    # required
                "description": "New list",                              # required
                "limit": 5,                                             # optional
                "cacheLifeTime": 30,                                    # optional
                "filters": "{\"metadata\":{\"located\":\"Porto\"}}"     # optional
            }
        ...