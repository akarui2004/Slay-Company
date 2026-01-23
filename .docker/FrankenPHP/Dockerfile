FROM dunglas/frankenphp:php8.4

# Install system dependencies and PHP extensions
# Added 'zip' (PHP ext) and OS-level unzip/7z for Composer
RUN install-php-extensions \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    pdo_mysql \
    redis \
    soap \
    exif

# Install OS tools for reliable archive extraction (Composer prefers these)
RUN set -eux; \
    if command -v apt-get >/dev/null 2>&1; then \
        export DEBIAN_FRONTEND=noninteractive; \
        apt-get update; \
        apt-get install -y --no-install-recommends unzip p7zip-full; \
        rm -rf /var/lib/apt/lists/*; \
    elif command -v apk >/dev/null 2>&1; then \
        apk add --no-cache unzip p7zip; \
    else \
        echo "Unsupported base image: neither apt-get nor apk found" >&2; \
        exit 1; \
    fi

# Set working directory
WORKDIR /app

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files (copies from project root due to context: .)
COPY . .

# Install dependencies
# Using --no-scripts to prevent "Illuminate\Foundation\ComposerScripts" errors during build
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Create a startup script directly in the Dockerfile
# This ensures caching runs at container startup, not build time
RUN echo '#!/bin/sh' > /usr/local/bin/start-container && \
    echo 'set -e' >> /usr/local/bin/start-container && \
    echo 'echo "Caching configuration..."' >> /usr/local/bin/start-container && \
    echo 'php artisan package:discover --ansi' >> /usr/local/bin/start-container && \
    echo 'php artisan config:cache' >> /usr/local/bin/start-container && \
    echo 'php artisan route:cache' >> /usr/local/bin/start-container && \
    echo 'php artisan view:cache' >> /usr/local/bin/start-container && \
    echo 'echo "Starting Octane..."' >> /usr/local/bin/start-container && \
    echo 'exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000' >> /usr/local/bin/start-container && \
    chmod +x /usr/local/bin/start-container

# Expose the internal port Octane runs on
EXPOSE 8000

# Use the custom startup script as the container command
CMD ["/usr/local/bin/start-container"]
