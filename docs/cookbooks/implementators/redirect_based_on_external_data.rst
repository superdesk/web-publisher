[Migration] Redirect articles from previous url's to publisher
==============================================================

Almost always after content migration you need to make sure that all previous links to articles works ok with new system.
In many publishing systems articles are identified by article/post numbers or some special codes. In Publisher we use unique
combination of route and article slug's. Because of that it's impossible to redirect requests only with server redirects.

Publisher provide solution for this case. In short: based on package external data (imported from external system) we
localize articles and return redirect responses with link to new article location.

External data
`````````````
With special API endpoint (secured with secret code) You can associate pair's of keys and values with imported article (package).

Example of setting external data (from our behat feature):


.. code-block:: bash

    When I add "Content-Type" header equal to "application/json"
    And I add "x-publisher-signature" header equal to "sha1=0dcd1953d72dda47f4a4acedfd638a3c58def7bc"
    And I send a "PUT" request to "/api/v1/packages/extra/test-news-article" with body:
    """
    {
      "articleNumber": "123456",
      "some other key": "some other value"
    }
    """

    Then the response status code should be 200

Redirect url
````````````

In our case server is responsible for composing redirect (supported with regular expressions) url containing article identifier from original url
and pushed as external data to package.

For example for article with url like that: :code:`/en/sport/123456/mundial-winner` we need to push indetifier (:code:`123456`) as a eternal data
to :code:`/api/v1/packages/extra/mundial-winner`.

After that server can rediret our url to this one: :code:`/redirecting/extra/articleNumber/123456`. Publisher in response will return Redirect
response (with code 301) to new article location.