# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### App install steps

```
cp .env.example .env
php artisan migrate
php artisan passport:install --force
php artisan db:seed # optional

# Manually create an app key in .env (32chars)
# Set correct DB creds and app URL.
```

### Using DNS to access the application
If you wish to use a DNS name instead of `http://127.0.0.1:8090` to access the application, you can install nginx locally and use reverse proxy.  
Create a file: `/etc/nginx/conf.d/docker.conf`  
And add this configuration to the bottom:
```
server {
  listen       filezone.docker:80;
  server_name  filezone.docker;
  location / {
    proxy_pass http://127.0.0.1:8090;
  }
}
```
You'll need to restart nginx after making any changes to the configuration file.  
```
sudo service nginx restart

# Allow httpd/nginx to make network connections
sudo setsebool -P httpd_can_network_connect 1
```  
If you do use a reverse proxy to access the site in this way, the `app_url` in the `.env` will need to match the `listen` directive you use in nginx.
