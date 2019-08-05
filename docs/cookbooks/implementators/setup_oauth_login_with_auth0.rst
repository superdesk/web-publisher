Setup oAuth login with auth0.com
================================

Publisher from version 2.0 have support for authentication via oAuth protocol. Here is an example of configuration with auth0.com

Let's assume that you have publisher instance under: :code:`https://www.publisher.wip` url.

First create fee account on auth0.com and create new Application of type :code:`Regular Web Applications`. As technology choose PHP.

Now let's configure required env variables in Publisher. Go to settings tab in your application page and look for variables defined bellow.

.. note::

    IMPORTANT: Add this url :code:`https://www.publisher.wip/connect/oauth/check`  to :code:`Allowed Callback URLs` field. And click :code:`Save changes` at the bottom of settings page.


In file :code:`.env.local` set those variables:

:code:`EXTERNAL_OAUTH_CLIENT_ID=<value of Client ID>`

:code:`EXTERNAL_OAUTH_CLIENT_SECRET=<value of Client Secret>`

:code:`EXTERNAL_OAUTH_BASE_URL=<value of Domain (with https://)`

Now go to https://www.publisher.wip/connect/oauth.

And it's done. After redirect to auth0, logging with selected provider - You will be redirected back to publisher as an authenticated user.
