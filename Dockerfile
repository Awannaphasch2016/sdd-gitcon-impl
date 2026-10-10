FROM php:8.2-apache

RUN a2enmod rewrite \
    && printf '%s\n' \
        '<VirtualHost *:80>' \
        '    DocumentRoot /var/www/app/public' \
        '    RewriteEngine On' \
        '    # /contact is a directory, so mod_dir 301s it to /contact/ before' \
        '    # per-directory rewrites. That drops the POST body. Route it here,' \
        '    # in the URL translation phase, so the contact handler still runs.' \
        '    RewriteRule ^/contact$ /index.php [END]' \
        '    <Directory /var/www/app/public>' \
        '        AllowOverride None' \
        '        Require all granted' \
        '        DirectoryIndex index.php' \
        '        RewriteEngine On' \
        '        RewriteOptions AllowNoSlash' \
        '        RewriteRule ^contact$ /index.php [END]' \
        '        RewriteCond %{REQUEST_METHOD} !=GET' \
        '        RewriteRule ^contact/submissions\.json$ /index.php [L]' \
        '        RewriteRule ^contact/submissions\.json/$ /contact/submissions.json [L]' \
        '        RewriteCond %{REQUEST_FILENAME} !-f' \
        '        RewriteRule ^ /index.php [L]' \
        '    </Directory>' \
        '    ErrorLog ${APACHE_LOG_DIR}/error.log' \
        '    CustomLog ${APACHE_LOG_DIR}/access.log combined' \
        '</VirtualHost>' \
        > /etc/apache2/sites-available/000-default.conf

COPY public/ /var/www/app/public/
COPY src/ /var/www/app/src/
COPY data/ /var/www/app/data/

RUN chown -R www-data:www-data /var/www/app/data /var/www/app/public/contact \
    && chmod 775 /var/www/app/data /var/www/app/public/contact

EXPOSE 80
