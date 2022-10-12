FROM nginx:1.17-alpine

RUN rm /etc/nginx/conf.d/default.conf

COPY ./ /var/www
COPY ./uffizzi/nginx/nginx /etc/nginx/conf.d/

