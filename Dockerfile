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



# Debian - Install Git (MC v1)

RUN apt-get update
RUN apt-get -y install git



# 2023-08-11
# Matt Calkins
#
#       Colby uses Git during setup and tests and potentially other times. This
#       is under question currently. Further decisions will be made when all
#       installations are running in a Docker container. Update this comment
#       when decisions are made.
#
#       This command allows git to run without error when running in a Docker
#       container.

RUN git config --system --add safe.directory /var/www/html



#
# - - - - - - - - - - development - - - - - - - - - -
#

FROM base AS development



# prepare for installs

RUN apt-get update



# make the "en_US.UTF-8" locale
# https://hub.docker.com/_/debian

RUN apt-get install -y locales
RUN rm -rf /var/lib/apt/lists/*
RUN localedef \
    -i en_US \
    -c \
    -f UTF-8 \
    -A /usr/share/locale/locale.alias \
    en_US.UTF-8
ENV LANG en_US.utf8



# ack

RUN apt-get update
RUN apt-get install -y ack



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



# Node.js, JSHint
# https://github.com/nodesource/distributions#debinstall

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

RUN npm install -g jshint



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

RUN git config --global pull.ff only
