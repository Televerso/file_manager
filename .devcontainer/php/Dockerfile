FROM php:7.4-fpm
WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql

# # Устанавливаем расширения
# RUN docker-php-ext-install pdo_mysql

# # Устанавливаем Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# # Копируем файлы проекта в контейнер
# COPY . .

# # Устанавливаем зависимости
# RUN composer install --no-interaction

# # Права на папку runtime
# RUN chown -R www-data:www-data runtime/ web/assets/ \
#     && chmod -R 755 runtime/ web/assets/