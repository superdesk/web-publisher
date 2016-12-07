Article Parsing
---------------

Instant Article look
````````````````````

For Instant Article rendering this template file is used :code:`/platforms/facebook_instant_article.html.twig`.
Basic version is provided by Publisher but you can (should) override it with your theme.

To control how exactly look pushed to Facebook Instant Articles API article you can use preview url.

:code:`/facebook/instantarticles/preview/{articleId}` - shows how article template was converted into FBIA article.
Parser is removing all not recognized tags. Read more about allowed rules here: https://developers.facebook.com/docs/instant-articles/sdk/transformer.

By default we use standard SDK rules: https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/src/Facebook/InstantArticles/Parser/instant-articles-rules.json