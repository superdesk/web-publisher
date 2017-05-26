Security Issues
===============

This document explains how security issues affecting code in the main ``superdesk/web-publisher`` `Git
repository`_ are handled by the Superdesk Publisher core team.

Reporting a Security Issue
--------------------------

If you think that you have found a security issue in Superdesk Publisher, don't use the
bug tracker and don't publish it publicly. Instead, all security issues must
be sent to **security [at] superdesk.org**. Emails sent to this address are
forwarded to the Superdesk Publisher core-team private mailing list.

Resolving Process
-----------------

For each report, we first try to confirm the vulnerability. When it is
confirmed, the core team works on a solution following these steps:

#. Send an acknowledgement to the reporter;
#. Work on a patch;
#. Write a security announcement for the official Superdesk Publisher `blog`_ about the
   vulnerability. This post should contain the following information:

   * a title that always include the "Security release" string;
   * a description of the vulnerability;
   * the affected versions;
   * the possible exploits;
   * how to patch/upgrade/workaround affected applications;
   * credits.
#. Send the patch and the announcement to the reporter for review;
#. Apply the patch to all maintained versions of Superdesk Publisher;
#. Package new releases for all affected versions;
#. Publish the post on the official Superdesk Publisher `blog`_

.. note::

    Releases that include security issues should not be made on a Saturday or
    Sunday, except if the vulnerability has been publicly posted.

.. note::

    While we are working on a patch, please do not reveal the issue publicly.

.. _Git repository: https://github.com/superdesk/web-publisher
.. _blog: https://www.superdesk.org/news
