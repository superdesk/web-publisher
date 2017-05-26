Users registration and login
============================

Registration
------------

User can be registered in Publisher with REST API :code:`/{version}/users/register/` POST request.

.. code-block:: bash

    curl -X POST 'http://webpublisher.dev/api/v1/users/' -H 'Origin: http://webpublisher.dev' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Accept: */*' -H 'Connection: keep-alive' -H 'DNT: 1' --data '_format=json&user_registration%5Bemail%5D=pawel.mikolajczuk%40sourcefabric.org&user_registration%5Busername%5D=pawel.mikolajczuk&user_registration%5BplainPassword%5D%5Bfirst%5D=superStronP%40SSword&user_registration%5BplainPassword%5D%5Bsecond%5D=superStronP%40SSword' --compressed

After that user will get email message with link for account confirmation. Link will redirect him to :code:`/register/confirmed` page.

Customize sender email address:
```````````````````````````````

By default email will be sent from ``'contact@{tenant domain}`` - example: ``contact@example.com``. You can override it by customizing ``registration_from_email.confirmation`` setting.


Customize confirmation email template:
``````````````````````````````````````

Default template used for confirmation is ``@FOSUser/Registration/email.txt.twig`` You can override it by customizing ``registration_from_email.confirmation`` setting.

Customize account confirmation page template:
`````````````````````````````````````````````

After clicking on conformation link (from email) user will be redirected to ``/register/confirmed`` url. To render this page publisher by default use ``'@FOSUser/Registration/confirmed.html.twig``.

You can override it in Your theme (with creating ``FOSUser/Registration/confirmed.html.twig`` file in your theme.


.. note::

    Read more about settings in :doc:`../../bundles/SWPSettingsBundle/index`.

Login
-----

Publisher don't provide single page for login action, instead that we made sure that login can be placed in any template (or even widget) of You choice. Only hardcoded url's for security are :code:`/security/login_check` (used for user authentication - you need to send your login form data there) and :code:`/security/logout` (used for logging out user).

Example login form:
```````````````````

.. code-block:: twig

    {% if app.session.has('_security.last_error') %}
        {# show error message after unsuccessful login attempt #}
        {% set error = app.session.get('_security.last_error') %}
        <div>{{ error.messageKey|trans(error.messageData, 'security') }}</div>
    {% endif %}
    <form action="{{ path('security_login_check') }}" method="POST">
        <input type="text" name="_username" value="{{ app.session.get('_security.last_username') }}" />
        <input type="password" name="_password" value="" />
        <input type="hidden" name="_login_success_path" value="{{ url('homepage') }}">
        <input type="hidden" name="_login_failure_path" value="{{ url('homepage') }}">
        <input type="submit" value="Login" />
    </form>

Parameters explanation:

 * :code:`_username` - User username parameter (You can use :code:`{{ app.session.get('_security.last_username') }}` as a default value - it will be filled with previously entered username when login will fail and user will be redirected to login form again).
 * :code:`_password` - User password
 * :code:`_login_success_path` - url used for redirection after successful login
 * :code:`_login_failure_path` - url used for redirection after unsuccessful login (use :code:`{{ url(gimme.page) }}` to generate url for current page)

Check if users is logged in:
````````````````````````````

.. code-block:: twig

    {% if app.user %}
        Hey {{ app.user.username }}. <a href="{{ url('security_logout') }}">Logout</a>.
    {% else %}
        {# Show login form #}
    {% endif %}
