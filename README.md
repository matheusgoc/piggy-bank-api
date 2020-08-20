# Piggy Bank API

A RESTful API Web Service to support Piggy Bank App

Piggy Bank API is a back-end Web Service based on REST API protocol 
to serve a mobile App for a final project called 
Personal Expenses Tracking App (PETA) 
from Illinois State University (ISU)

## PETA Project

The purpose of this project is to build a mobile App to serve as 
an easy and enjoyable tool to help people have more control over 
their outgoings and savings by allowing them to handle 
transactions over time and review their spending 
through some reports, graphs, 
and statistical insights.

## Software

This API adopts the last version of the following open-source software:

- [PHP Language](http://php.net)
- [Laravel Framework](https://laravel.com)
- [MySql Database](https://www.mysql.com)

## API Resources

This API provides the following resources:

- Profile
- Transactions
- Sub Transactions
- Partners
- Categories
- Reports
- Banking Accounts
- Banking Accounts Transactions

## Set up

After cloning the code, you need to do the following 
steps to set an environment:

1. Install the dependencies:

    ### `composer install`

2. Edit the .env file and set up your own environment:

    ### `cp .env.example .env`
    _e.g. database connection_
    
3. Create the database structure:

    ### `php artisan migrate`
    
4. Generate the Application Key

    ### `php artisan key:generate`

5. Grant write permission to the storage folder:

    ### `chmod a+U storage`
    
6. Generate a link for public storage:

    ### `php artisan storage:link`

## Run

The easy way to run the API is simply running the server 
that comes with the PHP. You can do it by executing 
the following artisan command:

### `php artisan serve`

You must be able to access the application from: http://localhost:8000

If you have set up a Web Server that runs PHP, such as Nginx or Apache, 
you can also run the API by defining a host for it. 
Just remember to point the host to the public folder
where the front controller is. 

See this link for more details: 

https://laravel.com/docs/master/installation

## Build

To deploy the API, the following optimizations should be performed:

- Autoloader
 
  ### `composer install --optimize-autoloader --no-dev`
 
- Configuration Loading

  ### `php artisan config:cache`
  
- Route Loading

  ### `php artisan route:cache`

- View Loading

  ### `php artisan view:cache`

See this link for more details: 

https://laravel.com/docs/master/deployment
