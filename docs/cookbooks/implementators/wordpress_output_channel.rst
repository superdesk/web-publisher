Setup Wordpress as a Publisher Output Channel
=============================================

Output channel allows you to use tenant as a bridge for publishing content in external systems.
Thanks to it content pushed to Publisher can be automatically published also in Wordpress or other
even internal systems.

We assume that Publisher is installed and running. So now we need Wordpress instance.

1. Download: :code:`wget https://wordpress.org/latest.zip`
2. Unpack: :code:`unzip latest.zip`
3. Download router.php file: :code:`cd wordpress && wget https://gist.githubusercontent.com/ginfuru/1dfd9a054f27d268e9e3f445896150f5/raw/9f5a4c71e9bd6592e113914e64f7c36c31c5a1ad/router.php`
4. Run Wordpress with built in php server: :code:`php -S wordpress.test:8080 router.php`
5. Install Wordpress
6. Install plugin "Application Passwords" (author: George Stephanis). Dont forget to activate it ;)
7. Go to :code:`http://wordpress.test:8080/wp-admin/profile.php` adn create new Application Password - it's on bottom of page.
7. Encode your password with :code:`echo -n "admin:8e7M k22B znze mLVF 3vmc i4Vc" | base64` (run this in terminal)
9. Copy generated password (we will use it later).
10. Create new tenant for wordpress:

.. code-block:: json

      {
        "tenant": {
          "domainName": "yourpublisherdomain.com",
          "name": "Wordpress Output Channel",
          "subdomain": "validvhostsubdomain",
          "outputChannel": {
            "type": "wordpress",
            "config": {
              "url": "http://wordpress.test:8080",
              "authorization_key": "Basic YWRtaW46OGU3TSBrMjJCIHpuemUgbUxWRiAzdm1jIGk0VmM="
            }
          }
        }
      }

In :code:`authorization_key` we type "Basic " and generated before password.

From now all content published to created tenant will be published, unpublished and updated in Wordpress instance.