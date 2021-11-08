# Laravel - Docker - Api

### Requirements
 - Docker

## Setup local environment
```
    # Clone source from github
    git clone https://github.com/tcdangkhoa/laravel-with-docker.git

    # Strat and run docker to create dev environment and run composer install , migrate
    Go to root folder of project and run bellow command : 
        + docker build ./docker
        + docker-compose up -d

    # Create .env file
        + Copy and rename file .env.example to .env file

    # Access to API
        - API endpoint to create a user : http://localhost:7000/signup
            + Body : email , name , password
            + Body type : form-data
            + Method : post

        - API endpoint to login : http://localhost:7000/login
            + Body : email , password
            + Body type : form-data
            + Method : post

        - API endpoint to create a wager : http://localhost:7000/wagers

        - API endpoint to accept buying a wager : http://localhost:7000/buy/{wagerId}

        - API endpoint to list wagers : http://localhost:7000/wagers?page={page}&limit={limit}

    # Run unit test
    vendor/bin/phpunit ./tests/Unit/api/WagersControllerTest.php

```

## Noted
    - I use another port(7000) instead 8080
    - You need to create a user and login with this user to get the JWT token , after that you can use this token to access to another api
    - With each api(exclude api signup and login user) JWT token is required , so you need to attached the JWT token with api , you have 2 ways to attached the JWT api : 
        + Authorization header : Authorization: Bearer eyJhbGciOiJIUzI1NiI...
        + Query string parameter : http://example.dev/me?token=eyJhbGciOiJIUzI1NiI...
    - About unit test : actual i want to do that better , but don't have more time , because at this moment i'm very busy ... so i have ignored