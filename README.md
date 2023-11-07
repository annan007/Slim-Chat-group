## Description

This project is a simple PHP chat backend implemented using the Slim Framework and SQLite database. It provides RESTful APIs to allow users
 - To create chat groups. 
 - Join these groups. [Groups are public hence any user can join any group]
 - Send messages within them.
 - To list all the messages with a group.
 - The user is identified via an ID.


## Objective
Listed are the objectives achieved in this assessment:
 - Clean code with helpful comments
 - Secure and well Structured
 - Easy to understand
 - Scalable

## Assumption
This project assumes that some sort of userID is accessible in the request body. 

## Installation

1. Pre-requisites:
   - PHP 8.1
   - composer 2.5.8
   - SQLite Database
   - Virtual Environment : For the development purpose Miniconda was used but an alternative could be phpenv.
   ```
   $ conda create --name bunq-assessment php
   ```


2. Install the project dependencies using Composer:
   ```
    $ composer install
   ```

 
3. Set up the SQLite database:
- To setup the SQLite databases and initialize all the tables run the below command
  ```
  $ php config/init-database.php  
  ```

1. Start the development server:
    ```
    $ composer start
    ```

2. For unit testing you can make use of the PHPUnit.
> Note: Due to some missing packages in my system I wasn't able to use PHPunit. Hence used Postman instead.

1. To access all the API's used in the repo use the following Postman Documentation:

    Link -> https://documenter.getpostman.com/view/10336444/2s93z6ejcg
    
