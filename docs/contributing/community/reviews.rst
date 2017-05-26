Community Reviews
=================

Superdesk Publisher is an open-source project driven by its community. If you don't feel
ready to contribute code or patches, reviewing issues and pull requests (PRs)
can be a great start to get involved and give back. In fact, people who "triage"
issues are the backbone to Superdesk Publisher's success!

Why Reviewing Is Important
--------------------------

Community reviews are essential for the development of the Superdesk Publisher project.
On the Superdesk Publisher JIRA_ bug tracker and GitHub_, you can find many items to work on:

* **Bug Reports**: Bug reports need to be checked for completeness.
  Is any important information missing? Can the bug be *easily* reproduced?

* **Pull Requests**: Pull requests contain code that fixes a bug or implements
  new functionality. Reviews of pull requests ensure that they are implemented
  properly, are covered by test cases, don't introduce new bugs and maintain
  backwards compatibility.

Note that **anyone who has some basic familiarity with Symfony and PHP can
review bug reports and pull requests**. You don't need to be an expert to help.

Be Constructive
---------------

Before you begin, remember that you are looking at the result of someone else's
hard work. A good review comment thanks the contributor for their work,
identifies what was done well, identifies what should be improved and suggests a
next step.

Create a GitHub Account
-----------------------

Superdesk Publisher uses GitHub_ to manage pull requests. If you want to
do reviews, you need to `create a GitHub account`_ and log in.

Create a JIRA Account
-----------------------

Superdesk Publisher uses JIRA_ to manage bug reports. If you want to
report a bug, you need to `create a JIRA account`_ and log in.


The Pull Request Review Process
-------------------------------

Reviews of pull requests usually take a little longer since you need
to understand the functionality that has been fixed or added and find out
whether the implementation is complete.

It is okay to do partial reviews! If you do a partial review, comment how far
you got and leave the PR in "needs review" state.

Pick a pull request from the `PRs in need of review`_ and follow these steps:

#. **Is the PR Complete**?
   Every pull request must contain a header that gives some basic information
   about the PR. You can find the template for that header in the
   :ref:`Contribution Guidelines <contributing-code-pull-request>`.

#. **Is the Base Branch Correct?**
   GitHub displays the branch that a PR is based on below the title of the
   pull request. Is that branch correct?

#. **Reproduce the Problem**
   Read the issue that the pull request is supposed to fix. Reproduce the
   problem on a clean Superdesk Web Publisher project and try to understand
   why it exists. If the linked issue already contains such a project, install
   it and run it on your system.

#. **Review the Code**
   Read the code of the pull request and check it against some common criteria:

   * Does the code address the issue the PR is intended to fix/implement?
   * Does the PR stay within scope to address *only* that issue?
   * Does the PR contain automated tests? Do those tests cover all relevant
     edge cases?
   * Does the PR contain sufficient comments to easily understand its code?
   * Does the code break backwards compatibility? If yes, does the PR header say
     so?
   * Does the PR contain deprecations? If yes, does the PR header say so? Does
     the code contain ``trigger_error()`` statements for all deprecated
     features?
   * Are all deprecations and backwards compatibility breaks documented in the
     latest UPGRADE-X.X.md file? Do those explanations contain "Before"/"After"
     examples with clear upgrade instructions?

   .. note::

       Eventually, some of these aspects will be checked automatically.

#. **Test the Code**

#. **Update the PR Status**

   At last, add a comment to the PR. **Thank the contributor for working on the
   PR**.

.. topic:: Example

    Here is a sample comment for a PR that is not yet ready for merge:

    .. code-block:: text

        Thank you @takeit for working on this! It seems that your test
        cases don't cover the cases when the counter is zero or smaller.
        Could you please add some tests for that?

.. _GitHub: https://github.com/superdesk/web-publisher
.. _JIRA: https://dev.sourcefabric.org/projects/SWP/issues
.. _create a JIRA account: https://login.sourcefabric.org/register
.. _create a GitHub account: https://help.github.com/articles/signing-up-for-a-new-github-account/
.. _forking: https://help.github.com/articles/fork-a-repo/
.. _PRs in need of review: https://github.com/superdesk/web-publisher/pulls?utf8=%E2%9C%93&q=is%3Apr+is%3Aopen+label%3A%22needs+review%22+
