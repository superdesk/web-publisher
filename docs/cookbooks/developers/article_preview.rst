Article Preview
===============

Article preview is based on user roles. Every article which is not published yet can be previewed by users with special roles assigned to them. This role is named ``ROLE_ARTICLE_PREVIEW``.

If a user has ``ROLE_ARTICLE_PREVIEW`` role assigned, he/she can preview article using url: ``domain.com/preview/article/<routeId>/<article-slug>/?auth_token=<token>``.

Where ``<routeId>`` is route identifier on which you want to preview given article by it's slug (``<article-slug>`` parameter).

Important here is to provide token, in order to be authorized to preview an article.

.. tip::

    See :doc:`API Authentication </internal_api/authentication>` section for more details on how to obtain user token.

For example, if you created an article that has a slug ``test-article`` and this article is assigned to ``news`` route which id is 5, it will be available for preview under ``/preview/article/5/test-article?auth_token=uty56392323==`` url but only when the user has ``ROLE_ARTICLE_PREVIEW`` role assigned. In other cases 403 error will be thrown.

If you are building JavaScript app and you want to preview article, the preview url of an article can be taken and loaded in an iframe for preview.

User roles eligible for article preview:
----------------------------------------

+-----------------------------------------------+
| Role                                          |
+===============================================+
| ``ROLE_EDITOR``                               |
+-----------------------------------------------+
| ``ROLE_INTERNAL_API``                         |
+-----------------------------------------------+
| ``ROLE_ADMIN``                                |
+-----------------------------------------------+
| ``ROLE_SUPER_ADMIN``                          |
+-----------------------------------------------+
