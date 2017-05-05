Submitting a Patch
==================

Patches are the best way to provide a bug fix or to propose enhancements to
Superdesk Publisher.

Step 1: Setup your Environment
------------------------------

Install the Software Stack
~~~~~~~~~~~~~~~~~~~~~~~~~~

Before working on Superdesk Publisher, setup a friendly environment with the following
software:

* Git;
* PHP version 5.5.9 or above;
* `PHPUnit`_ 4.2 or above.
* PostgreSQL

Configure Git
~~~~~~~~~~~~~

Set up your user information with your real name and a working email address:

.. code-block:: bash

    git config --global user.name "Your Name"
    git config --global user.email you@example.com

.. tip::

    If you are new to Git, you are highly recommended to read the excellent and
    free `ProGit`_ book.

.. tip::

    If your IDE creates configuration files inside the project's directory,
    you can use a global ``.gitignore`` file (for all projects) or a
    ``.git/info/exclude`` file (per project) to ignore them. See
    `GitHub's documentation`_.

.. tip::

    Windows users: when installing Git, the installer will ask what to do with
    line endings, and suggests replacing all LF with CRLF. This is the wrong
    setting if you wish to contribute to Superdesk Publisher! Selecting the as-is method is your best choice, as Git will convert your line feeds to the ones in the
    repository. If you have already installed Git, you can check the value of
    this setting by typing:

    .. code-block:: bash

        git config core.autocrlf

    This will return either "false", "input" or "true"; "true" and "false" being
    the wrong values. Change it to "input" by typing:

    .. code-block:: bash

        git config --global core.autocrlf input

    Replace --global by --local if you want to set it only for the active
    repository

Get the Superdesk Publisher Source Code
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Get the Superdesk Publisher source code:

* Create a `GitHub`_ account and sign in;

* Fork the `Superdesk Publisher repository`_ (click on the "Fork" button);

* After the "forking action" has completed, clone your fork locally
  (this will create a ``web-publisher`` directory):

.. code-block:: bash

      git clone git@github.com:USERNAME/web-publisher.git

* Add the upstream repository as a remote:

.. code-block:: bash

      cd web-publisher
      git remote add upstream git://github.com/superdesk/web-publisher.git

Check that the current Tests Pass
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that Superdesk Publisher is installed, check that all tests pass for your
environment as explained in the dedicated :doc:`document <tests>`.

Step 2: Work on your Patch
--------------------------

The License
~~~~~~~~~~~

Before you start, you must know that all the patches you are going to submit
must be released under the GNU AGPLv3 license, unless explicitly specified in your
commits.

Create a Topic Branch
~~~~~~~~~~~~~~~~~~~~~

Each time you want to work on a patch for a bug or an enhancement, create a
topic branch:

.. code-block:: bash

    git checkout -b BRANCH_NAME master

.. tip::

    Use a descriptive name for your branch, containing the ticket number from the bug tracker.

The above checkout commands automatically switch the code to the newly created
branch (check the branch you are working on with ``git branch``).

Work on your Patch
~~~~~~~~~~~~~~~~~~

Work on the code as much as you want and commit as much as you want; but keep
in mind the following:

* Read about the Superdesk Publisher :doc:`conventions <conventions>` and follow the
  coding :doc:`standards <standards>` (use ``git diff --check`` to check for
  trailing spaces -- also read the tip below);

* Add unit tests to prove that the bug is fixed or that the new feature
  actually works;

* Try hard to not break backward compatibility (if you must do so, try to
  provide a compatibility layer to support the old way) -- patches that break
  backward compatibility have less chance to be merged;

* Do atomic and logically separate commits (use the power of ``git rebase`` to
  have a clean and logical history);

* Never fix coding standards in some existing code as it makes the code review
  more difficult;

* Write good commit messages (see the tip below).

.. tip::

    When submitting pull requests, `StyleCI`_ checks your code
    for common typos and verifies that you are using the PHP coding standards
    as defined in `PSR-1`_ and `PSR-2`_.

    A status is posted below the pull request description with a summary
    of any problems it detects or any Travis CI build failures.

.. tip::

    A good commit message is composed of a summary (the first line),
    optionally followed by a blank line and a more detailed description. The
    summary should start with the Component you are working on in square
    brackets (``[MultiTenancy]``, ``[MultiTenancyBundle]``, ...). Use a
    verb (``fixed ...``, ``added ...``, ...) to start the summary and don't
    add a period at the end.

Prepare your Patch for Submission
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When your patch is not about a bug fix (when you add a new feature or change
an existing one for instance), it must also include the following:

* An explanation of the changes in the relevant ``CHANGELOG`` file(s) (the
  ``[BC BREAK]`` or the ``[DEPRECATION]`` prefix must be used when relevant);

* An explanation on how to upgrade an existing application in the relevant
  ``UPGRADE`` file(s) if the changes break backward compatibility or if you
  deprecate something that will ultimately break backward compatibility.

Step 3: Submit your Patch
-------------------------

Whenever you feel that your patch is ready for submission, follow the
following steps.

Rebase your Patch
~~~~~~~~~~~~~~~~~

Before submitting your patch, update your branch (needed if it takes you a
while to finish your changes):

.. code-block:: bash

    git checkout master
    git fetch upstream
    git merge upstream/master
    git checkout BRANCH_NAME
    git rebase master


When doing the ``rebase`` command, you might have to fix merge conflicts.
``git status`` will show you the *unmerged* files. Resolve all the conflicts,
then continue the rebase:

.. code-block:: bash

    git add ... # add resolved files
    git rebase --continue

Check that all tests still pass and push your branch remotely:

.. code-block:: bash

    git push --force origin BRANCH_NAME

.. _contributing-code-pull-request:

Make a Pull Request
~~~~~~~~~~~~~~~~~~~

You can now make a pull request on the ``superdesk/web-publisher`` GitHub repository.

To ease the core team work, always include the modified components in your
pull request message, like in:

.. code-block:: text

    [MultiTenancy] fixed something
    [Common] [MultiTenancy] [MultiTenancyBundle] added something

The pull request description must include the following checklist at the top
to ensure that contributions may be reviewed without needless feedback
loops and that your contributions can be included into Superdesk Publisher as quickly as
possible:

.. code-block:: text

    | Q             | A
    | ------------- | ---
    | Bug fix?      | [yes|no]
    | New feature?  | [yes|no]
    | BC breaks?    | [yes|no]
    | Deprecations? | [yes|no]
    | Tests pass?   | [yes|no]
    | Fixed tickets | [comma separated list of tickets fixed by the PR]
    | License       | AGPLv3

An example submission could now look as follows:

.. code-block:: text

    | Q             | A
    | ------------- | ---
    | Bug fix?      | no
    | New feature?  | no
    | BC breaks?    | no
    | Deprecations? | no
    | Tests pass?   | yes
    | Fixed tickets | #12, #43
    | License       | AGPLv3

The whole table must be included (do **not** remove lines that you think are
not relevant). For simple typos, minor changes in the PHPDocs, or changes in
translation files, use the shorter version of the check-list:

.. code-block:: text

    | Q             | A
    | ------------- | ---
    | Fixed tickets | [comma separated list of tickets fixed by the PR]
    | License       | GPLv3

Some answers to the questions trigger some more requirements:

* If you answer yes to "Bug fix?", check if the bug is already listed in the
  Superdesk Publisher bug tracker and reference it/them in "Fixed tickets";

* If you answer yes to "New feature?", you must submit a pull request to the
  documentation and reference it under the "Doc PR" section;

* If you answer yes to "BC breaks?", the patch must contain updates to the
  relevant ``CHANGELOG`` and ``UPGRADE`` files;

* If you answer yes to "Deprecations?", the patch must contain updates to the
  relevant ``CHANGELOG`` and ``UPGRADE`` files;

* If you answer no to "Tests pass", you must add an item to a todo-list with
  the actions that must be done to fix the tests;

* If the "license" is not as AGPLv3 here, please don't submit the pull request as it won't
  be accepted anyway.

If some of the previous requirements are not met, create a todo-list and add
relevant items:

.. code-block:: text

    - [ ] fix the tests as they have not been updated yet
    - [ ] submit changes to the documentation
    - [ ] document the BC breaks

.. caution::

    When submitting pull requests which require some documentation changes, please
    also update the documentation where appropriate, as it is kept in the same repository (`documentation dir`_)

If the code is not finished yet because you don't have time to finish it or
because you want early feedback on your work, add an item to the todo-list:

.. code-block:: text

    - [ ] finish the code
    - [ ] gather feedback for my changes

As long as you have items in the todo-list, please prefix the pull request
title with "[WIP]".

In the pull request description, give as much detail as possible about your
changes (don't hesitate to give code examples to illustrate your points). If
your pull request is about adding a new feature or modifying an existing one,
explain the rationale for the changes. The pull request description helps the
code review and it serves as a reference when the code is merged (the pull
request description and all its associated comments are part of the merge
commit message).

Rework your Patch
~~~~~~~~~~~~~~~~~

Based on the feedback on the pull request, you might need to rework your
patch. Before re-submitting the patch, rebase with ``upstream/master``, don't merge; and force push to the origin:

.. code-block:: bash

    git rebase -f upstream/master
    git push --force origin BRANCH_NAME

.. note::

    When doing a ``push --force``, always specify the branch name explicitly
    to avoid messing with other branches in the repo (``--force`` tells Git that
    you really want to mess with things, so do it carefully).

If moderators asked you to "squash" your commits, this means you will need to convert many commits to one commit.

.. _ProGit: http://git-scm.com/book
.. _GitHub: https://github.com/join
.. _`GitHub's Documentation`: https://help.github.com/articles/ignoring-files
.. _Superdesk Publisher repository: https://github.com/superdesk/web-publisher
.. _travis-ci.org: https://travis-ci.org/
.. _`travis-ci.org status icon`: http://about.travis-ci.org/docs/user/status-images/
.. _`travis-ci.org Getting Started Guide`: http://about.travis-ci.org/docs/user/getting-started/
.. _`documentation dir`: https://github.com/superdesk/web-publisher/tree/master/docs
.. _`StyleCI`: https://styleci.io/
.. _`PSR-1`: http://www.php-fig.org/psr/psr-1/
.. _`PSR-2`: http://www.php-fig.org/psr/psr-2/
.. _PHPUnit: https://phpunit.de/manual/current/en/installation.html
