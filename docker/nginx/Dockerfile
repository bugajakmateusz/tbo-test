FROM nginx:1.23.3-alpine AS prod

RUN mkdir /var/www/html/public -p
RUN adduser -u 1000 -D -S -G www-data www-data

COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./conf.d/templates/tab.template.conf /etc/nginx/conf.d/templates/tab.template.conf
COPY ./h5bp /etc/nginx/h5bp
COPY ./mime.types /etc/nginx/mime.types

FROM prod AS dev
ARG DEPLOY_NAME
ARG CUSTOM_CONFIG
RUN envsubst '${DEPLOY_NAME}' < /etc/nginx/conf.d/templates/tab.template.conf > /etc/nginx/conf.d/dev.conf \
    && sed -i "s/###custom-config###/$CUSTOM_CONFIG/g" /etc/nginx/conf.d/dev.conf \
    && rm -f /etc/nginx/conf.d/default.conf
