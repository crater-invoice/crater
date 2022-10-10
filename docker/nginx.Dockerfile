FROM nginx:1.23.1-alpine

COPY docker/nginx /etc/nginx/conf.d/
RUN rm /etc/nginx/conf.d/default.conf
COPY public/ /var/www/public/ 