Article Preview
===============

Article preview is based on user privileges. Every article which is not published yet can be previewed by users with special privileges assigned to them. If a user has, for example, ``ROLE_EDITOR`` role, he/she can preview article using url: ``domain.com/<section>/<article-slug>``.

For example, if you created an article that has a slug ``test-article`` and is assigned to ``news`` route, it will be available for preview under ``/news/test-article`` url but only when the user has ``ROLE_CAN_VIEW_NON_PUBLISHED`` role assigned. In other cases 404 error will be thrown.

If you are building JavaScript app and you want to preview article, the preview url of an article can be taken from articles API (``/content/articles/``), from ``_links`` JSON object - ``online`` link and loaded in an iframe for preview.

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
