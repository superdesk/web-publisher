web: $(composer config bin-dir)/heroku-php-nginx -C etc/heroku/nginx_host.conf public/
worker: php bin/console messenger:consume async_content_push async_analytics_event async_webhooks async_analytics_export async_image_conversion async_apple_news --time-limit=3600
