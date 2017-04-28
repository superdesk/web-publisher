(Un)publishing content
====================================

Deciding if content is visible for user in Publisher is done dynamically - based on user roles.

.. note::

    By default all users can see only published content, but if user have role :code:`ROLE_CAN_VIEW_NON_PUBLISHED` then also not published content will be fetched and rendered.

How published content is marked in database?
--------------------------------------------

Published content need to match those criteria:

 * :code:`status` property set to :code:`published`
 * :code:`publishedAt` property filled with date and time
 * :code:`isPublishable` property set to :code:`true`

How to Publish content?
-----------------------

*By REST API call*

.. code-block:: php

    curl -X "PATCH" -d "article[status]=published" -H "Content-type:\ application/x-www-form-urlencoded" /api/v1/content/articles/get-involved

*By code*

.. code-block:: php

    // $this->container - instance of Service Container
    // $article - ArticleInterface implementation
    $articleService = $this->container->get('swp.service.article');
    $articleService->publish($article);

How to check (in code) if content is published?
-----------------------------------------------

.. code-block:: php

    use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
    ....
    $publishWorkflowChecker = $this->serviceContainer->get('swp.publish_workflow.checker');

    if ($publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ATTRIBUTE, $article)) {
        // give access for article
    }

.. note::

    Content assigned to routes is automatically checked (if it's publishable for current user) by event listener.


How to Un-publish Content?
--------------------------

If an article is published you can easily un-publish it via API as described above. Another way to un-publish already published article is by ``killing`` the article. It can be achieved by setting the value of ``pubStatus`` property to ``canceled`` in the JSON (Ninjs) content according to `IPTC standards <http://cv.iptc.org/newscodes/pubstatusg2/canceled>`_. Once that status will be set, and the content will be send to Publisher (/content/push API endpoint), article will be un-published immediately and its status will be set to ``canceled``.
