Authentication
--------------

To work with Facebook Instant Articles REST API you need to have valid access token. Bundle provides controller for
:code:`authentication url generation` and :code:`handling authorization callback requests from Facebook`.

Authorization procedure requires providing Facebook Application and Page (token will be generated for that combination) ID's.
Bundle provide entities for both (Application and Page) and will look for matching rows in database.

.. note::

    Authentication controller checks if provided page and application are in your storage (database). But bundle doesn't
    provide controllers for adding them (there are only pre-configured factories and repositories) - You need to implement it manually in your application.


Authentication flow
```````````````````

.. include:: /bundles/SWPFacebookInstantArticlesBundle/authentication_flow.rst
