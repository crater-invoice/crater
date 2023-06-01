ARG BASE_IMAGE

FROM $BASE_IMAGE as build
FROM nginx:1.17-alpine

RUN rm /etc/nginx/conf.d/default.conf

COPY --from=build /var/www/html /var/www/html
COPY ./uffizzi/nginx/nginx /etc/nginx/conf.d/
