#==================
# PHP Dependencies
#==================
FROM composer:1.9.3

COPY database database/
COPY tests tests/

COPY composer.json composer.json
COPY composer.lock composer.lock

ARG uid=1000
RUN addgroup -g $uid -S devuser && \
    adduser -u $uid -S devuser -G devuser

ARG github_api_token=none
ENV COMPOSER_AUTH {\"github-oauth\":{\"github.com\":\"$github_api_token\"}}

USER devuser

WORKDIR /var/www
