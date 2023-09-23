## Description

This project is a simple PHP chat backend implemented using the Slim Framework and SQLite database. It provides APIs to manage chat groups, join groups, and send/receive messages within groups.

## Assumption
This project assumes that some sort of userID is accessible in the request body. 

## Installation

1. Pre-requisites:
   - php
   - composer
   - SQLite Database


2. Install the project dependencies using Composer:
   ```
    $ composer install
   ```

 
3. Set up the SQLite database:
- The database file `chat_app.db` is already included in the `src/database` directory.
- If you want you can create a new one and use that as well.

4. Start the PHP development server:
    ```
    $ php -S localhost:8000 -t public
    ```

5. For unit testing you can make use of the PHPUnit.
> Note: Due to some incompatibility issue regarding my working system I wasn't able to use PHPunit. Hence used Postman instead.

6. To access all the API's used in the repo use the following Postman Documentation:

    Link -> https://documenter.getpostman.com/view/10336444/2s93z6ejcg
    
