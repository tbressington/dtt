## Running the app

```console
$ cp .env.example .env
$ composer install
$ ./vendor/bin/sail up -d
$ ./vendor/bin/sail artisan migrate:install
$ ./vendor/bin/sail artisan migrate
$ ./vendor/bin/sail artisan db:seed
$ ./vendor/bin/sail artisan key:generate
```

## Running the tests

I've opted to use PEST so both Unit and Feature tests are run using a single command, and is reported in a nice easy to read format.

```console
$ ./vendor/bin/pest
```