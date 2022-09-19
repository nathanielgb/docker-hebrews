FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    npm \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

    
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#Node Installation
RUN npm install npm@latest -g && \
    npm install n -g && \
    n latest


# Install PHP extensions
RUN docker-php-ext-install intl pdo_mysql mbstring exif pcntl bcmath

# Get latest Composer
COPY --from=composer:2.2.12 /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
