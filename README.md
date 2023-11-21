# Clean up
```
docker run --rm \
-u "$(id -u):$(id -g)" \
-v $(pwd):/opt \
-w /opt \
laravelsail/php80-composer:latest \
composer install --ignore-platform-reqs
```

# Install

- Assumes you have docker and docker-compose installed as per guidelines on docker website

## Both

``` cp .env.example .env ```

## Linux

``` docker-compose up -d --build ```

## Windows

- Modify `./docker/8.0-fpm/Dockerfile` to comment out line 30

```Dockerfile
# USER www
```

``` docker-compose up -d --build ```

- Revert `./docker/8.0-fpm/Dockerfile` to its original state

## Both

- ssh into backend container
  ``` 
    docker exec -it challenge /bin/sh 
  ```
- in the container
    ```
      composer install
      php artisan key:generate
    ```
- (re-)create database and add seed data
    ```
      php artisan migrate:fresh --seed
    ```
# Recipes

## to start all services

```docker-compose up -d```

## ssh into a container

```docker exec -it <container_name> /bin/bash ```

or

```docker exec -it <container_name> /bin/sh```

## list running services

``` docker ps```

## view container logs

``` docker logs <container_name> --follow ```

# Shutdown

stop all services
``` docker-compose down ```

## Links

- http://localhost:8990

## Helpful

```
php artisan route:cache

php artisan config:cache

php artisan cache:clear

```
