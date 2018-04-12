Secure content push to Publisher
================================

Content can be pushed to Publisher via HTTP requests from any system. But it's important to store and publish only requests
from approved by us sources.

To verify incoming requests we use special header: :code:`x-superdesk-signature`. Value of this header have format like
that: :code:`sha1={token}`.

:code:`token` is a result of HMAC (keyed-Hash Message Authentication Code) function. It's
created from request content and :code:`secret token` value with sha1 algorithm applied on it.

:code:`secret token` can be defined in Organization (when created or updated). Example command:

.. code-block:: bash

    php app/console swp:organization:create -u --secretToken secret_token

.. note::

   :code:`-u` parameter activates update mode

Organization secret token is not visible in any API or in command.

If token is set in organization then Publisher will reject all requests without :code:`x-superdesk-signature` header or
with wrong value in it.