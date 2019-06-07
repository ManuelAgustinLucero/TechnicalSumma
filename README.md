# Technical exercise the Company Summa
Points:
* Add employees
* Obtain a list of all Employees
* Search by Id and get an Employee
* Get the average age of the employees
# Description 

Project created with Symfony 3.4 API REST

Database mysql.

## Install with Composer

```
    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install or composer install
```

## Setting Environment
When the composer installation is finished
```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:schema:update --force
    $ You can config database in app/config/parameters.yml

```

## How to start
```
    $ php bin/console server:run
```

## Resources Api

**Enterprise Api**

>GET /api/enterprise

>GET /api/enterprise/1

>POST /api/enterprise/new

>PUT /api/enterprise/edit/1

>DELETE /api/enterprise/remove/1

Body:

```json
   {
     "name":"Summa"
   }
```
**Employee Api**

>GET /api/employee

>GET /api/employee/1

>DELETE /api/employee/remove/1

**Developer Api**

>GET /api/developer

>GET /api/developer/1

>POST /api/developer/new

>PUT /api/developer/edit/1

>DELETE /api/developer/remove/1

Body:

```json
   {
     "name":"Manuel",
     "lastName":"Lucero",
     "phone":"358487654",
     "email":"Summa@summa.com",
     "birth_date":"1995-11-27",
     "type": 1,
     "enterprise": 1 

   }
```
**Designer Api**

>GET /api/designer

>GET /api/designer/1

>POST /api/designer/new

>PUT /api/designer/edit/1

>DELETE /api/designer/remove/1

**Average Api**

>GET /api/average/all

>GET /api/average/byEnterprise/1


## Observations
There are 3 types of language
```json
   types: {
     1: "PHP",
     2: "NET",
     3: "Python",
    }
```
There are 2 types of designer
```json
   types: {
     1: "Gráfico",
     2: "Web",
   }
```
## License
[MIT](https://choosealicense.com/licenses/mit/)
