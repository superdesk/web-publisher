Rules
=====

Rules are a way to define the business logic inside the system. They define how that business logic should be organized.
A good example is a rule defining an article auto-publish. The organization rule can be configured to forward the content
received from Superdesk to one of the defined tenants. Then tenant's rule can be configured to automatically publish
an article under specific route, so if the content is received in Superdesk Publisher,
it can be automatically forwarded to the defined tenants (it won't be published by default)
and will be published under the specific route defined by the tenant's rule.

There are two types of rules which are defined in Superdesk Publisher:
- organization rules - applied to the level of organization
- tenant rules - applied to the level of tenant

Auto-publish content based on rules
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

1. Adding an organization rule to push content to the desired tenants
---------------------------------------------------------------------

Let's assume there is a single organization created which contains a single tenant with code ``123abc``.

The next step is to create an organization rule by making a ``POST`` request to the ``/api/v1/organization/rules/`` API endpoint
with the JSON body:

.. code-block:: json

    {
       "name":"Test rule",
       "description":"Test rule description",
       "priority":1,
       "expression":"package.getLocated() matches \"/Sydney/\"",
       "configuration":[
          {
             "key":"destinations",
             "value":[
                {
                   "tenant":"123abc"
                }
             ]
          }
       ]
    }

In the JSON above we define that if the content which comes from Superdesk has a field ``located`` and it matches the value of ``Sydney``,
then push the content to the tenant with the code equal to ``123abc``.

On the tenant level, a new article will be created based on the pushed content which won't be published by default.
Right now, this article can be manually published or route can be assigned to it manually etc.

In order to publish it automatically, read below.


2. Adding a tenant rule to autopublish content under specific route
-------------------------------------------------------------------

The next step is to create a rules on the level of tenant to automatically publish the article under specific route.

In order to do that, a ``POST`` request must be made to the ``/api/v1/rules/`` API endpoint with the JSON body:

.. code-block:: json

    {
       "name":"Test tenant rule",
       "description":"Test tenant rule description",
       "priority":1,
       "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
       "configuration":[
          {
             "key":"route",
             "value":6
          },
          {
             "key":"published",
             "value":true
          }
       ]
    }

The above JSON string defines, if an article's metadata field called ``located`` equals to ``Sydney`` then
assign route with id ``6`` to the article and automatically publish it.

3. Adding a tenant rule to publish content to Facebook Instant Articles
-----------------------------------------------------------------------

Article can be also published to Facebook Instant Articles. To do that, create a new tenant rule by making a ``POST``
request to the ``/api/v1/rules/`` API endpoint with the JSON body:

.. code-block:: json

    {
       "name":"Test tenant rule",
       "description":"Test tenant rule description",
       "priority":1,
       "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
       "configuration":[
          {
             "key":"fbia",
             "value":true
          }
       ]
    }

Note the ``fbia`` key in the ``configuration`` property is set to ``true``.

If the content will be pushed to the tenant, the content will be also submitted to the Facebook Instant Articles.

Read more about Facebook Instant Articles in :doc:`this section </cookbooks/editors/configure_facebook_instant_articles>`.

4. Adding a tenant rule to make an article paywall-secured
----------------------------------------------------------

Articles can be marked as paywall-secured so an access can be restricted to such articles.
To do that, create a new tenant rule by making a ``POST``
request to the ``/api/v1/rules/`` API endpoint with the JSON body:

.. code-block:: json

    {
       "name":"Make articles paywall-secured",
       "description":"Marks articles as paywall-secured.",
       "priority":1,
       "expression":"article.getMetadataByKey(\"located\") matches \"/Sydney/\"",
       "configuration":[
          {
             "key":"paywallSecured",
             "value":true
          }
       ]
    }

Note the ``paywallSecured`` key in the ``configuration`` property is set to ``true``.

If the content will be pushed to the tenant and will match given expression, the "paywall-secured" flag will be set to ``true``.

Read more about Paywall in :doc:`this section </cookbooks/developers/paywall>`.

Evaluation of the rules which match given package/item
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Based on the package, there is a possibility to evaluate rules that match given package's/item's metadata.
If the package/item in NINJS format will be passed to the ``/api/v1/organization/rules/evaluate`` as a request's payload,
then the (organization and/or tenant) rules that match that package's/item's metadata will be returned.
