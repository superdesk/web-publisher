Git
===

This document explains some conventions and specificities in the way we manage
the Superdesk Publisher code with Git.

Pull Requests
-------------

Whenever a pull request is merged, all the information contained in the pull
request (including comments) is saved in the repository.

You can easily spot pull request merges as the commit message always follows
this pattern:

.. code-block:: text

    merged branch USER_NAME/BRANCH_NAME (PR #11)

The PR reference allows you to have a look at the original pull request on
GitHub: https://github.com/superdesk/web-publisher/pull/11. But all the information
you can get on GitHub is also available from the repository itself.

The merge commit message contains the original message from the author of the
changes. Often, this can help understand what the changes were about and the
reasoning behind the changes.
