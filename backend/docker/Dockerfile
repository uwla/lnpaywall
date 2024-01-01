FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

# sync timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# install dependencies
RUN apt-get update \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y ca-certificates curl dnsutils fswatch git gnupg gosu libcap2-bin libpng-dev librsvg2-bin python2 sqlite3 supervisor unzip zip

# install php8
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.3-cli php8.3-dev \
       php8.3-pgsql php8.3-sqlite3 php8.3-gd \
       php8.3-curl \
       php8.3-imap php8.3-mysql php8.3-mbstring \
       php8.3-xml php8.3-zip php8.3-bcmath php8.3-soap \
       php8.3-intl php8.3-readline \
       php8.3-ldap

# mode
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3

# install composer
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# expose port where backend will be available at
EXPOSE 8080

# add non-root user
RUN useradd --create-home \
    --user-group \
    --shell /bin/bash \
    --uid 1000 \
    laravel

# create dir for app
RUN mkdir /app && chown -R laravel:laravel /app

# switch user and workdir
WORKDIR /app
USER laravel

# default cmd
CMD ["bash", "-c", "composer install && php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port=8080"]
