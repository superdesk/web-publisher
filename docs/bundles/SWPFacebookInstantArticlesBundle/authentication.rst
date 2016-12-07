Authentication
--------------

To work with Facebook Instant Articles REST API you need to have valid access token. Bundle provides controller for
:code:`authentication url generation` and :code:`handling authorization callback requests from Facebook`.

Authorization procedure requires providing Facebook Application and Page (token will be generated for that combination) ID's.
Bundle provide entities for both (Application and Page) and will look for matching rows in database.

TODO: Write about adding page and application to database

Authentication flow
```````````````````

Assuming that in your database you have Application with id :code:`123456789` and Page with id :code:`987654321`
(and both it exists on Facebook platform), You need to call this url :code:`(route: swp_fbia_authorize)`:

.. code-block:: text

    /facebook/instantarticles/authorize/123456789/987654321

In response You will be redirected to Facebook where You will need allow for all required permissions.

After that Facebook will redirect You again to application where (in background - provided by Facebook :code:`code` will
be exchanged for access token and that access) you will get JSON response with :code:`pageId` and :code:`accessToken`
(never expiring access token).
