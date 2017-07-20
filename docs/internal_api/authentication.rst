API authentication
==================

Internal API endpoints require user authentication (user need to have :code:`ROLE_INTERNAL_API` role assigned).

Authentication data (token) must be attached to every request with :code:`Authorization` header or :code:`auth_token` query
parameter.

Get authentication token for registered user
--------------------------------------------

To get authentication token you need to call  :code:`/api/v1/auth` with your :code:`username` and :code:`password` - in response you will
get your user information's and token data.

Example:

.. code-block:: bash

    curl 'http://publisher.dev/api/v1/auth' -d 'auth%5Busername%5D=username&auth%5Bpassword%5D=password' --compressed

.. note::

    Publisher token will be valid for 48 hours

Get authentication token for superdesk user
-------------------------------------------

To get authentication token you need to call  :code:`/api/v1/auth/superdesk` with superdesk legged in user
:code:`session_id` and :code:`token` - in response you will get your user information's and token data.

Example:

.. code-block:: bash

    curl 'http://publisher.dev/api/v1/auth/superdesk' -d 'auth_superdesk%5Bsession_id%5D=5831599634d0c100405d84c7&auth_superdesk%5Btoken%5D=Basic YTRmMWMzMTItODlkNS00MzQzLTkzYjctZWMyMmM5ZGMzYWEwOg==' --compressed

Publisher in background will ask authorized superdesk server for user session (and user data). If Superdesk will confirm
session information then Publisher will get internal user (or create one if not exists) and create token for him.

.. note::

    Publisher token will be this same as the one from superdesk (provided in :code:`/api/v1/auth/superdesk` request).

Generate Authentication URL for Livesite Editor
-----------------------------------------------

You can create with API special authentication URL for tenant website. To do that you need to call :code:`/api/v1/livesite/auth/livesite_editor`
as authorized user (with token in request header or url).

.. code-block:: bash

    curl 'http://publisher.dev/api/v1/livesite/auth/livesite_editor' -H 'Authorization: d6O3UorCHZ2Pd8PRs/0aXGg1qnT0bKUPWW43dgKqYm3CI4U4Og==' --compressed

In response you will get JSON with Your token details and special URL which can be used for authentication and Livesite Editor activation.

After following that url you will be redirected to tenant homepage. Meantime special cookie with name :code:`activate_livesite_editor` will be set.
This cookie will have API token set as it's value. It would best if you will set token value in browser local storage and
remove cookie (so it will not be send to server with every request).
