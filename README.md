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

3. Run migration and seeders
```
sail artisan migrate --seed
```

4. Accessing the API
The API will be available here http://127.0.0.1/api
If you prefer not to use an local ip or local host, then add:
`127.0.0.1   social.test` to `\etc\hosts`

5. Running test
This application uses the Laravel Pest tests
```
sail artisan test
```

## API Endpoints

```
GET|HEAD    api/posts ................................ posts.index › PostController@index
  PUT       api/posts/{post} ......................... posts.update › PostController@update
  DELETE    api/posts/{post} ......................... posts.destroy › PostController@destroy
  GET|HEAD  api/posts/{post} ......................... posts.show › PostController@show
  PATCH     api/posts/{post}/like .................... posts.like › PostController@like
  PATCH     api/posts/{post}/unlike .................. posts.unlike › PostController@unlike
  POST      api/token/fetch .......................... TokenController@fetch
  POST      api/token/revoke ......................... TokenController@revoke
  GET|HEAD  api/users ................................ users.index › UserController@index
  GET|HEAD  api/users/{id} ........................... users.show › UserController@show
  PATCH     api/users/{user}/follow .................. users.follow › UserController@follow
  PATCH     api/users/{user}/unfollow ................ users.unfollow › UserController@unfollow
```

###
Testing via postman (Default password is in .env)
- emeka@test.com
- john@test.com
- simon@test.com

NB: The .env will be commit for demo purpose

1. Send a POST request to fetch a token
```
api/token/fetch

payload:
email:emeka@test.com
password:
```

2. Use token, in the header
```
Authorization Bearer <token>

Example
Authorization Bearer 1|IIoR9zCDPNaIaWQIxptc9YwcD0dp1RB1a8WAC1KZ
```

NB: Preferable way of testing is by running tests