 * PHP = 8.0
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
   * php.ini recommended settings
     * short_open_tag = Off
     * magic_quotes_gpc = Off
     * register_globals = Off
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
 * Supervisor
