# SETUP

This document will serve as a setup guide for this appilcation.

## Requirements
- PHP ^8.1
- Laravel 10
- Docker
- Laravel Sail
- Composer

## Setup

1. From the root of the application install packages and dependcies by running
```
composer install
```

2. Start the Laravel sail docker container by running. 
```
sail up -d
```

This will try to run the application on the following ports
- 80    : Web server
- 3306  : MySQL server
- 6379  : Redis server
- 11211 : Memcached

3. Accessing the API
The API will be available here http://127.0.0.1/api
If you prefer not to use an local ip or local host, then add:
`127.0.0.1   social.test` to `\etc\hosts`

4. Running test
This application uses the Laravel Pest tests
```
sail artisan test
```

## API Document