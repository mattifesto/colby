#
# - - - - - - - - - - base - - - - - - - - - -
#

FROM php:8.0-apache AS base

# TODO 20230708T0934
# Matt Calkins
#
# Create a production target. This is the copy command for creating a production
# container from a dev environment. It is not correct for creating a fresh dev
# environment.
#
# COPY . /var/www/html

ENV CB_DISABLE_SSL true

RUN apt-get update


# GD

RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libpng-dev
RUN apt-get install -y libwebp-dev
RUN apt-get install -y zlib1g-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

RUN docker-php-ext-install gd


# MySQL

RUN docker-php-ext-install mysqli


# rewrite / htaccess

RUN a2enmod rewrite



#
# - - - - - - - - - - development - - - - - - - - - -
#

FROM base AS development



# install git

RUN apt-get -y update
RUN apt-get -y install git



# install GigHub CLI (gh)
# https://github.com/cli/cli/blob/trunk/docs/install_linux.md

RUN type -p curl >/dev/null || (apt-get update && apt-get install curl -y)
RUN curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg \
    && chmod go+r /usr/share/keyrings/githubcli-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | tee /etc/apt/sources.list.d/github-cli.list > /dev/null \
    && apt-get update \
    && apt-get install gh -y



# install PHP composer
# https://hub.docker.com/_/composer/

COPY --from=composer /usr/bin/composer /usr/bin/composer


# install zip tools for PHP composer
# comoposer technically works without this but takes longer and emits warnings

RUN apt-get install -y libzip-dev
RUN apt-get install -y unzip
RUN docker-php-ext-install zip



# install docker
# https://docs.docker.com/engine/install/debian/

RUN apt-get remove docker.io
RUN apt-get remove docker-compose
RUN apt-get remove docker-doc

# The linked instructions say to remove this but it isn't actually installed and
# causes an error when building the Docker image.
#
#   "Unable to locate package podman-docker"
#
# RUN apt-get remove podman-docker

RUN apt-get install -y ca-certificates
RUN apt-get install -y curl
RUN apt-get install -y gnupg

RUN install -m 0755 -d /etc/apt/keyrings
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | \
    gpg --dearmor -o /etc/apt/keyrings/docker.gpg
RUN chmod a+r /etc/apt/keyrings/docker.gpg

RUN echo \
    "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
    "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" | \
    tee /etc/apt/sources.list.d/docker.list > /dev/null

RUN apt-get update

RUN apt-get install -y docker-ce
RUN apt-get install -y docker-ce-cli
RUN apt-get install -y containerd.io
RUN apt-get install -y docker-buildx-plugin
RUN apt-get install -y docker-compose-plugin



# add developer user
# https://code.visualstudio.com/remote/advancedcontainers/add-nonroot-user

ARG USERNAME=devuser
ARG USER_UID=1000
ARG USER_GID=$USER_UID

RUN groupadd \
    --gid 1000 \
    $USERNAME
RUN useradd \
    --uid $USER_UID \
    --gid $USER_GID \
    --create-home \
    --shell /bin/bash \
    $USERNAME

RUN apt-get update
RUN apt-get install -y sudo
RUN echo $USERNAME ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME
RUN chmod 0440 /etc/sudoers.d/$USERNAME

USER $USERNAME

COPY --chown=devuser:devuser . /home/devuser/php_composer_repositories/colby

COPY --chown=devuser:devuser ./dev_website /var/www/html

WORKDIR /var/www/html

RUN git config --global pull.ff only

RUN composer require mattcalkins/colby
