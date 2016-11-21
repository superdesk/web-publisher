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

.. code-block:: php

    curl 'http://publisher.dev/api/v1/auth' -d 'auth%5Busername%5D=username&auth%5Bpassword%5D=password' --compressed

.. note::

    Publisher token will be valid for 48 hours

Get authentication token for superdesk user
-------------------------------------------

To get authentication token you need to call  :code:`/api/v1/auth/superdesk` with superdesk legged in user
:code:`session_id` and :code:`token` - in response you will get your user information's and token data.

Example:

.. code-block:: php

    curl 'http://publisher.dev/api/v1/auth/superdesk' -d 'auth_superdesk%5Bsession_id%5D=5831599634d0c100405d84c7&auth_superdesk%5Btoken%5D=Basic YTRmMWMzMTItODlkNS00MzQzLTkzYjctZWMyMmM5ZGMzYWEwOg==' --compressed

Publisher in background will ask authorized superdesk server for user session (and user data). If Superdesk will confirm
session information then Publisher will get internal user (or create one if not exists) and create token for him.

.. note::

    Publisher token will be this same as the one from superdesk (provided in :code:`/api/v1/auth/superdesk` request).