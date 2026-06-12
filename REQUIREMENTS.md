 * PHP >= 8.3 (8.5 recommended, used by the official Docker image)
   * iconv needs to be enabled
   * Intl needs to be installed with ICU 4+
   * pdo needs to be enabled
   * JSON needs to be enabled
   * ctype needs to be enabled
   * Your php.ini needs to have the date.timezone setting
   * PHP tokenizer needs to be enabled
   * mbstring functions need to be enabled
   * POSIX needs to be enabled (only on *nix)
   * CURL and php-curl need to be enabled
   * GD, zip, fileinfo, sodium, sysvsem and sockets need to be enabled
   * php.ini recommended settings
     * short_open_tag = Off
     * session.auto_start = Off
 * Postgresql >= 9.6
   * pdo-pgsql
 * Memcached
   * memcached (running)
   * php-memcached
 * ElasticSearch >= 7.0
 * RabbitMQ >= 3.5
    * php-bcmath
    * php-amqp (`pecl install amqp`)
 * Mercure hub (e.g. `dunglas/mercure`) for realtime package notifications
 * Supervisor
