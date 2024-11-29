FROM hacklab/mapasculturais:7.5.12

COPY themes /var/www/src/themes
COPY plugins /var/www/src/plugins

WORKDIR /var/www/src
RUN pnpm install --recursive && pnpm run build 

COPY docker/common/config.d /var/www/config/common.d
COPY docker/common/lgpd-terms /var/www/config/lgpd-terms
COPY docker/production/config.d /var/www/config/config.d

COPY docker/common/php-ini.d /usr/local/etc/php/conf.d

WORKDIR /var/www
