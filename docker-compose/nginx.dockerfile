ARG NGINX_VERSION
FROM nginx:${NGINX_VERSION}

COPY public /var/www/html/public
COPY docker-compose/nginx/nginx.conf /etc/nginx/conf.d/nginx.conf
