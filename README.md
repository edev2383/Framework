# Framework

# Table of Contents

- [Connecting To The Database](README.md#connecting-to-the-database)

- [Getting Started](README.md#getting-started)

- [Creating A Route](https://github.com/jasoncases/dashboard/blob/master/README.md#creating-a-route)

- [Creating A Controller](https://github.com/jasoncases/dashboard/blob/master/README.md#create-a-controller-class)

- [ORM Layer](https://github.com/jasoncases/dashboard/blob/master/README.md#orm-layer)

  - [SELECT](https://github.com/jasoncases/dashboard/blob/master/README.md#select)
  
    - [WHERE](https://github.com/jasoncases/dashboard/blob/master/README.md#where-method)
  
    - [JOINS](https://github.com/jasoncases/dashboard/blob/master/README.md#joins)

  - [INSERT](https://github.com/jasoncases/dashboard/blob/master/README.md#save)

  - [UPDATE](https://github.com/jasoncases/dashboard/blob/master/README.md#update)

  - [DELETE](https://github.com/jasoncases/dashboard/blob/master/README.md#delete)

  - [ANONYMOUS MODELS](https://github.com/jasoncases/dashboard/blob/master/README.md#anonymous-model)

- [Create a View](https://github.com/jasoncases/dashboard/blob/master/README.md#create-a-view)

- [Render the View](https://github.com/jasoncases/dashboard/blob/master/README.md#render-the-view)

  - [Templates In the View](https://github.com/jasoncases/dashboard/blob/master/README.md#templates-in-the-view)

  - [If Statements](https://github.com/jasoncases/dashboard/blob/master/README.md#if-statements)

  - [For...Each Loops](https://github.com/jasoncases/dashboard/blob/master/README.md#foreach-loops)

  - [Includes](https://github.com/jasoncases/dashboard/blob/master/README.md#template-includes)

[File System](https://github.com/jasoncases/dashboard/blob/master/README.md#file-system)

- [Namespaces](https://github.com/jasoncases/dashboard/blob/master/README.md#namespaces)

  - [Resource](https://github.com/jasoncases/dashboard/blob/master/README.md#resource)

    - [Display](https://github.com/jasoncases/dashboard/blob/master/README.md#display)

    - [MISC-Handler](https://github.com/jasoncases/dashboard/blob/master/README.md#handler)

    - [Crypto](https://github.com/jasoncases/dashboard/blob/master/README.md#crypto)

    - [DotEnv](https://github.com/jasoncases/dashboard/blob/master/README.md#dotenv)

    - [Status](https://github.com/jasoncases/dashboard/blob/master/README.md#status)

    - [Uid](https://github.com/jasoncases/dashboard/blob/master/README.md#uid)

    - [Token](https://github.com/jasoncases/dashboard/blob/master/README.md#token)

    - [Templater](https://github.com/jasoncases/dashboard/blob/master/README.md#templater)

  - [Repository(ies) - "Repos"](https://github.com/jasoncases/dashboard/blob/master/README.md#repository)


## Getting Started

### Connecting to the Database

In general, the Controllers you are working in will handle their own connections via the **Edev\Database\Manager\Manager()**. However, there are certain instances where you may have to manually create your connections.

The only place a connection is being established outside of a Controller is in an Email piping/parsing subroutine, which is unique to a certain project. 

```php
/**
* The Conn class uses the .env variables to connect to the database. You can access the .env values using the
* [DotEnv() class](https://github.com/jasoncases/dashboard/blob/master/README.md#dotenv), but the Conn method
* uses a process loop that handles all of that for you.
* **/

new \Edev\Database\Manager\Conn();

### Creating A Route

Routes are REST end points that can either render a View on the client, or return data, usually in JSON format. 

> Route/RouteController.php

class RouteController is a Singleton instance and is instantiated at the top of the Route/Routes.php file. The static methods to create routes are below:

```php

/**
* Create a suite of routes around the uri string:
*     Example: $uri = 'test';  // www.example.com/test is now a route
*     Resource adds (7) routes to the application and point to specific methods within the associated Controller class.
*
*             Route           VERB            METHOD
*         1.) /test           [GET]        => index
*         2.) /test           [POST]       => store
*         3.) /test/create    [GET]        => create
*         4.) /test/{id}      [GET]        => show
*         5.) /test/{id}/edit [GET]        => edit
*         6.) /test/{id}      [PUT]        => update
*         7.) /test/{id}      [DELETE]     => destroy
*
*         NOTES: {id} = id number associated with the record, see {...TODO: link}
*
* @param string $uri           - route root example.com/[uri]
* @param string $controller    - controller name, must be PascalCase and map to ControllerName.php in Controller folder
* @param string $namespace     - additional namespace value for any Controller\* child classes
* @return void
*/
public static function resource($root, $controller, $namespace = null) { ... }

/**
* Create a single route that points to a specific method within the provided controller
*
* @param string $uri           - route root example.com/[uri]
* @param string $controller    - controller name, must be PascalCase and map to ControllerName.php in Controller folder
* @param string $method        - the method to be called via the uri, must be prefixed w/ lowercase $VERB
* @param string $verb          - HTTP METHOD, must be UPPERCASE ['GET', 'POST', 'PUT', 'DELETE']
*
* @return void
*/
public static function controller($uri, $controller, $method, $verb = 'GET'){
```

__All routes are contained within file /vendor/edev/src/Route/Routes.php__

```php
// Resource Routes
$route = \Route\Route::getInstance();

// Resource Group
$route::resource('fruit', 'FruitController');

// Controller Group expands the defined resource
$route::controller('fruit/eat', 'FruitController', 'getEatFruit'); // 'GET' is implied, so no 4th param needed

// POST HTTP method is stated and matches the lowercase prefix of the method name
$route::controller('fruit/types/add', 'FruitController', 'postAddFruitType', 'POST');
```

### Create a Controller class

> /Controller/FruitController.php

```php
<?php

namespace Edev\Controller;

/**
* A Controller "controls" the business logic for each specified route. They all extend the parent Controller class.
* They MUST be named using PascalCase, where each word is capitalized and they MUST end with the word 'Controller',
* otherwise, the RouteController.php cycle will fail and output a die('error').
*
* All Controllers MUST contain at minimum the (7) resource methods and the __construct() method.
*
* The below Controller contains the 'controller' methods we added using $route::controller() above.
*
** */
class FruitController extends \Edev\Controller\Controller
{
    protected $_viewPath = 'View/fruit';
   
    // BEGIN RESOURCE METHODS
    public function index(){}
    public function store(){}
    public function create(){}
    public function show(){}
    public function edit(){}
    public function update(){}
    public function destroy(){}

    // BEGIN CONTROLLER METHODS
    public function getEatFruit() {}
    public function postAddFruitType() {}
}
```

### ORM Layer

The ORM Layer is a Model layer that maps to the database. Each Child of the **\Edev\Model\Model** class, maps to exactly one database table. This ORM style is inspired by Laravel's ORM. 

```php
<?php

namespace Edev\Model;

class TestModel extends Model
{
  // Define the target db table
  protected $table = 'my_db_table_name';

  // define pdoName, default is 'client', other option is 'meta' if Model targets the Meta database layer
  protected $pdoName = 'client';
}
```

#### SELECT

To get data from our Models, we need to build the queries. Each Model is initiated and then calls a static method which primes the query builders

```php
// To get all values in a table
Employee::all();

// To get one column of all rows
Employee::all(['first_name']);

// To get a record by id, there is a shortcut method called getById()
Employee::getById(1);

// The longform version of the previous method would be:
Employee::where('id', 1)->get();

// Results can be refined by passing strings into the get() method that match field names in your table
Employee::where('status', 1)->get('id', 'first_name', 'last_name');

// andWhere, orWhere further modify the query builder
Employee::where('first_name', 'Frank')->andWhere('age', '>', 35)->get();
```

#### Where method

Some more details on the where methods

```php
/**
*
* @param string $column   - column name in the database table
* @param string $operator - comparison operator, defaults to '='
* @param mixed $value     - value for comparison
* */
public function where($column, $operator = null, $value = null)

// where can take 2 or 3 arguments, if given only 2, operator is assumed to be '='
// where('column', 2) is functionally idenical to where('column', '=', 2)

// Other version of where
whereNull($column)
whereNotNull($column)

// $array consists of two values where column should lie between
whereBetween($column, $array)

// $array consists of 1..n values
whereIn($column, $array)
whereNotIn($column, $array)
```

> **Upcoming:** I intend to add join() methods so we can produce some more complex queries, just haven't had the need as of yet, since I tend to create views when I need to do anything more complex than a single query. The way I see it, if I need to add joins to my query, it's probably complex and/or important enough to create a view.

#### Joins

__As a proto-concept, I've added a `LEFT JOIN` statement__

This will require further testing and development and I am sure to make some changes, but for now, we can create left joins w/ the following statement:

```php

$x = Timesheet::where('a.id', '>', 14000)
        ->leftJoin('ts_shifts', 'b', 'id', 'a.shift_id') 
        ->leftJoin('ts_payroll_completed', 'c', 'shift_id', 'b.id')
        ->get('a.id', 'b.created_at', 'c.created_at as closeTime', 'a.activity_id');

/**
* leftJoin($table, $as, $joinColumn, $comparisonColumn)
*  
* $table              - table name of the *joining* table
* $as                 - alias letter/name of the joining table, primary table ALWAYS defaults to 'a'
* $joinColumn         - field on the joining column for condition comparison
* $comparisonColumn   - field on whichever joined table column for condition comparison
* */
```

Currently join conditionals are limited to equality comparisons and when using you must be explicit about which tables you are targeting in your where() and get() methods. I think the goal would be to allow for less explicit statements, and allow the code to generate more of the query via internal logic, allowing the user to supply arguments in a different, more streamlined format. 

##### Additional Modifiers

```php
// return first or last record in table by provided column
last($column = null)
first($column = null)

// USAGE:
Employee::where('status', 1)->last()->get();

// "order by" methods, if no column provided, defaults to 'created_at', latest => 'DESC', oldest => 'ASC'
latest($column = null)
oldest($column = null)

// USAGE:
Employee::where('status', 1)->latest()->get();

// Get the last 'id' created:
Employee::lastId();
```

#### SAVE

To insert value into the table, use the ::save() method. A boolean value is returned.

```php
// to save, pass an array to ::save(), where the keys match required field names in the database table
$array = [ 'first_name' => 'Bobby', 'last_name' => 'Fischer', 'age' => 22 ];
Employee::save($array);
```

__IMPORTANT NOTE:__ On success __The Model::save()__ method now RETURNS the id of the last inserted row, a la PostgreSQL. It previously returned a boolean, but now the QueryBuilder::execute() method performs a secondary query on INSERT to get the MAX(id) value and return that. (This uses a new class Mock, to act as a decorator to the Model class)

##### Database keywords

The ORM layer can recognize certain keywords within your tables column names. If these field names are present, these values do not need to be explicitly stated in your Model query. The builder will inject them automatically. If you provide a value, your value will take priority and the injection will be bypassed.

```
  author - when you want to store the `employee_id` of the user logged in when creating the record
      - used in:
        - Model::save()
  manager_id - store the `employee_id` of the user logged in when creating the record
      - used in:
        - Model::save()
  edited_by - store the `employee_id` of the user logged in when editing the record
      - used in:
        - Model::update()
  deleted_by - store the `employee_id` of the user logged in during a soft delete action
      - used in:
        - Model::delete()
  _ip - store the request ip address on save
      - used in:
        - Model::save()
```

#### UPDATE

All client level database tables MUST use `id` as their auto_increment table, this is automatically added to the table when using PHINX database migration library. The query builder will look for the 'id' value and create the query to update those values, it will return a boolean value.

```php
$array = [ 'id' => 1, 'first_name' => 'Beowulf' ];
Employee::update($array);
```

#### DELETE

The delete method will `soft-delete` a record if a `deleted_at` field is present. If a record that has been soft-deleted is deleted again, it is destroyed for good. There are two delete processes to use.

```php
// When you know the id number of the record, use
Employee::delete(1);

// If your delete is reliant on other column values, you can use deleteWhere()
Employee::deleteWhere('first_name', '!=', 'Jeffery');

// ! - deleteWhere() behaves the same as the where() method when SELECTing, i.e., 2 params default the operator to '='

// If your delete is reliant on multiple column values, you can pass an array to deleteWhere()
Employee::deleteWhere(
  [
    [ 'first_name', '!=', 'Jeffery' ],
    [ 'age', '>', 35 ],
  ]
);
```

> Currently there isn't a way to use OR in the deleteWhere statement, but since this is an edgecase situation, it shouldn't be necessary. Can be added later if needed.

#### Anonymous Model

__Update (8.24):__ This process has now been wrapped into a new class \Edev\Model\Mock(). I want to do some additional testing before adding to the docs.

An edge case has popped up where we needed to access a database table that didn't have a model, or the model wasn't accessible with the current fileset. This is also useful for situations that may arise with testing a new table without creating a model file.

We were able to mock the model with the following code:

```php
// set table name
$table = 'this_is_my_table_name';

// instantiate a new Model object and set the Model table property
$mockModel = new Model();
$mockModel->setTable($table);

// use the model to create a builder class
/**
* We do it this way, rather than creating the Builder directly w/ the
* \Edev\Database\Model\Builder class because the Model->newModelBuilder()
* method handles Query builder instantiation, as well as resolving the db
* connection/pdo handler
* **/
$builder = $mockModel->newModelBuilder();

// set the Model for the builder
$builder->setModel($mockModel);

// Now we can access the query builder methods
$builder->where('id', 1)->get('some_field_name');
```

### Create a View

All views are contained within the **/View** folder. They are organized by folders named for their route uris, all lowercase. View files SHOULD BE html, but any file can be output. No PHP code can be contained within the rendered files, as it will simply be read and printed. The files can include javascript and the framework provides **includes**, **foreach loops** and **if statements** within the HTML code.

- [Includes]()
- [Foreach Loops]()
- [If/Else]()

```
|---/View
|   |--- /fruit
|   |   |---index.html
|   |   |---show.html
|   |   |---create.html
|   |   |---edit.html
```

Sample View

> /View/fruit/index.html

```html
<html>
  <head>
    <title>Sample Fruit View</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>
    <p>Hello World!</p>
  </body>
</html>
```

### Render the view

> /Controller/FruitController.php

```php
public function index()
{
    // render the index view
    $this->render('index.html');

    /**
    *   Controller::render($path, $data);
    *       - $path is the path to the file, typically just the file name.
    *       - render automatically appends the protected $_viewPath variable to the front of the provided path
    *            - This limitation is intentional.
    *            - If you are needing to render outside of the specific folder, a new Controller is probably needed
    *            - set $_viewPath to '', and then manually input all paths to bypass
    *       - $data is an optional array value, used to send data to the view for rendering {...TODO: link to sending data}
    ** */

}
```

### Templates in the View

We can send data to the view with our **render** method. Let's update our **/fruit/index.html** view.

```
You can template variables in the HTML by using curly brackets, placed around the name of the variable, i.e., {varName}
```

You can see in the below **HTML**, we've included two templates, "title" and "name"

```html
<html>
  <head>
    <title>{title}</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>
    <p>Hello World!</p>
    <p>My name is, {name}</p>
  </body>
</html>
```

Now in our Controller, we'll give the variables values and send those values to the HTML for rendering

> /Controller/FruitController.php

```php
public function index()
{
    // The $data array must be associative, otherwise the templates won't be able to map their values
    $this->render('index.html', ['title' => 'My Fruit View', 'name' => 'Steve']);
}
```

Now the View in the browser source will be:

```html
<html>
  <head>
    <title>My Fruit View</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>
    <p>Hello World!</p>
    <p>My name is, Steve</p>
  </body>
</html>
```

> Under the hood

_The Controller takes the \$data array and pushes all values in the array to a main view array named \_newData[]. The Display class then uses the \_newData array to format any templating instances within the HTML view. It's possible to add values to the \_newData array directly, but it's not suggested as it can cause the code to become unnecessarily complicated._

#### Update to templates in the View

_We can now use compound templates in the view, allowing us to access single-level array values as well as setting a default value when the requested value is null_

```php
  // We will send a single person to index.html with the following values
  $this->render('index.html', ['person' => ['name' => 'Travis', 'age' => 36', 'favcolor' => null]]);
```

```html
<!-- we access the properties using the ':' character -->
<p>Patient: {person:name}</p>
<p>Age: {person:age}</p>
<!-- If there is a value that could be null and you want to choose a default, use the '||' operator -->
<p>Favorite Color: {person:favcolor || No Color Provided}</p>
```

### If statements

We can currently perform simple if/else statements in the HTML code, allowing us to separate our concerns and let the render do the work for us. This is helpful so we don't have to explicitly determine values in our PHP source, rather put the options into the HTML and let the Display class decide what to render.
`Let's assume we need to render a "Welcome, {username}!" string if a user is logged in. In example #2, we will render a link to allow the user to log in using an else statement.`

> /Controller/FruitController.php

```php
public function index()
{
   // define our variable
   $logged_in = true;

    // The $data array must be associative, otherwise the templates won't be able to map their values
    $this->render('index.html', ['username' => 'Tomato_Enthusiast', 'logged_in' => $logged_in]);
}
```

Our values have been passed to the view for rendering, so lets look at the new markup in our **fruit/index.html** file.

> /View/fruit/index.html

```html
===== EXAMPLE #1 ======
<html>
  <head>
    <title>My Fruit View</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>
    <!-- use the @ symbol to denote the beginning of a command structure -->
    @if (logged_in == true)
    <!-- Whatever is placed between the @if...@endif will be printed if the statement evaluates -->
    <p>Welcome, {username}! @endif</p>
  </body>
</html>

===== EXAMPLE #2 ======
<html>
  <head>
    <title>My Fruit View</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>
    @if (logged_in == true)
    <p>Welcome, {username}! @else</p>

    <p><a href="/login">Click here to login!</a></p>
    @endif
  </body>
</html>
===========
```

##### More about @if statements

```
The following operations are valid:
==  - equal
!=  - not equal
>   - greater than
<   - less than
>=  - great than OR equal to
<=  - less than OR equal to

When the variable is a string evaluation, use double quotes (")
@if (string == "my string")
```

_Coming soon(ish)! We're planning to add nested if statements, as well as nested foreach and if statements within foreach loops, but there is no immediate timeline for these to be pushed upstream_

### for...each loops

Foreach loops allow us to output values from associative arrays right in the view logic, just like if statements. In this example, we're going to make use of the PHP method [**compact()**](https://www.php.net/manual/en/function.compact.php), which compresses a variable into named instance in an array. Coupled with the PHP method [**extract()**](https://www.php.net/manual/en/function.extract.php), you can end up writing fewer lines of code. _Be sure to only use extract on trusted data_

```php
// compact/extract Example
$animal = 'dog';
$years = 4;

$newArray = compact('animal', 'years');

print_r($newArray); // output = ['animal' => 'dog', 'years' => 4]

$extractArray = ['animal' => 'sealion', 'years' => 14]
extract($extractArray);
echo $animal; // output = 'sealion'
echo $years; // output = 14
```

> /Controller/FruitController.php

```php
public function index()
{
   $fruit = [
            ['type' => 'banana', 'price' => '$4.50',  'quantity' => 10],
            ['type' => 'apple',  'price' => '$3.50',  'quantity' => 6],
            ['type' => 'kiwi',   'price' => '$14.50', 'quantity' => 3],
            ];

    // The $data array must be associative, otherwise the templates won't be able to map their values
    $this->render('index.html', compact('fruit'));

    /**
    * In this example, we're only sending the one array to the view. If we had to send more than just the one array to the view,
    * we'd have to place it within a container array
    * $this->render('index.html', [compact('fruit'), 'logged_in' => false, 'date' => time()]);
    * */
}
```

Now we'll update our **/View/fruit/index.html**

```html
<html>
  <head>
    <title>My Fruit View</title>
  </head>
  <body>
    <h2>This is my FruitController index page!</h2>

    <!-- the value in the parenthesis must match the name of the array or an error will throw -->
    @foreach (fruit)
    <!-- inside the loop, we can access the values via the same templating used above -->
    <h3>{type}</h3>
    <p>Price: {price}</p>
    <p>Qty Remaining: {quantity}</p>
    @endforeach
  </body>
</html>
```

### Includes

We can also template some of our reused HTML and include it within the views. All templates are kepted in the folder:

> /View/template/

> Example: /View/template/style_headers.html

```html
<style>
  .container {
    background-color: blue;
  }
</style>
```

> /View/fruit/index.html

```html
<html>
  <head>
    <title>My Fruit View</title>
    <!-- This will include /View/template/style_headers.html -->
    @include(style_headers)
  </head>
  <body>
    ...
  </body>
</html>
```

It is suggested to separate your main views into templates for any repetative HTML. Current Edev view files tend to follow this layout:

# UPDATE - 05-01-2020

**We now use a layout pattern and the controller has a defaul layout for all pages, this can be adjusted by adding an attribute to the render method.**

```html
$layout = 'special_layout'; $this->render('page.html', $dataArray, $layout);

<!-- The render method is smart enough to swap attributes, so if you have no data, you can just make the layout value the second attribute -->
$this->render('page.html', 'my_new_layout');
```

> Layout files MUST BE placed in /View/layout

_@if and @foreach do work in included files_

# File System

> This has had some major changes to structure. Notably, any folders/files having to do with the php framework are now in /vendor/proaction/src

```
|--- /public_html
|   |--- /vendor
|   |   |--- /edev   
|   |   |   |--- /src
|   |   |   |   |--- /App
|   |   |   |   |--- /Controller
|   |   |   |   |   |--- Controller.php
|   |   |   |   |   |--- MetaController.php
|   |   |   |   |   |--- ModuleController.php
|   |   |   |   |   |--- /Installer
|   |   |   |   |   |--- /Throwable

|   |   |   |   |--- /Controller
|   |   |   |   |--- /Interface
|   |   |   |   |--- /Middleware
|   |   |   |   |--- /Repository
|   |   |   |   |--- /Resource
|   |   |   |   |--- /Route
|   |   |   |   |--- /Throwable
|   |   |   |--- autoload.php
|   |--- /View
|   |--- /css
|   |--- /img
|   |--- /js
|   |--- /module
|   |--- index.php

Note: The capitalized folders are the Namespaced filesystem directories
Note: Not all files present are listed above
```

# Namespaces

## Resource

Files located in the **/Resource** folder are in the **Resource** namespace.

This namespace is reserved for classes that perform various levels of work, within the filesystem. Some of these classes have very narrow scopes and perform a very specific set of actions, while others can perform a wider range of actions, but are focused on one area of responsibility. We'll begin with a brief overview of all of the classes within the Resource namespace and we'll link to more in-depth explanations as they become available.

### Display

Display class handles all rendering of the views. Each view is a newly instantiated Display instance, accepting a truncated path and an option array of data. The Controller::render() method is responsible for creating the Display object to render the view.

### [...]Handler

There are many []Handler classes, focused on specific areas of responsibility, i.e., UserHandler has methods specific to creating users, verifying user log-in status, etc. Specific Handlers will get more details as documentation expands.

##### GlobalHandler()

This handler resource class uses a repository that points to the `client_globals` database table. These globals are system variables unique to each client. Some are immutable, while others are able to be changed by the client via an administration endpoint. It is registered in the main Controller class as **\$this->global**.

```php
  // the `client_globals` table is organized as such:
  // | id | constant | value | . . . | default |

  $this->global->get($string); // where $string = the constant to be retrieved.

  // some examples are 'path_to_root', 'max_break_hours', 'break_alert'
```

There is an update method, but that is called primarily in the SettingsController from the administration endpoint.

### Crypto

A static class for two-way encryption using a private key ::encrypt(), ::decrypt()

```php
  $key = 'THIS IS MY KEY';
  $string = 'testing';

  $encrypted = \Edev\Resource\Crypto::encrypt($string, $key);

  echo $encrypted; // output: mP6zcTXmXDQU44UUt+7GAN9Sj7ba6yE=

  $decrypted = \Edev\Resource\Crypto::decrypt($encrypted, $key);

  echo $decrypted; // output: 'testing'
```

### DotEnv

A static class for accessing .env variables

```php

   $key = 'DB_HOST';

   // where $key is a value in the .env file in the root of the project.
   $host = \Edev\Resource\DotEnv::get($key);

   // DotEnv requires keys to be UPPER_CASE, no numbers, only special character allowed is underscore ( _ )
   // DB_HOST=127.0.0.1
```

### Status

Creates a response session value which can be accessed by the Core.js prototype on the client-side. This displays pop-up statuses that are set within the controllers use the message($message, $status) method.

### Uid

Used to generate unique ids (uid) rather than simple auto-incremented ids. For external facing identifiers, i.e., in this application, the client ids, using uid strings as identifiers can prevent iteration attacks to compel data from the server. _Currently, internally, users and other identifiers are limited to auto_increment values_

```php
   /**
   * Returns a symbol divided string for unique identification, ex.: 'BHUOF-ID87D-JFL65'
   *
   * @param string $prefix    - Identifier prefix, Admin users get prefix 'AU'
   * @param int $segLength    - Number of characters between the symbols
   * @param int $totalLengh   - Total length of the resulting string, takes into account the prefix length and # of symbols
   * @param string $symbol    - Symbol to use between segments
   *
   * @return string
   * **/
   \Edev\Resource\Uid::create($prefix, $segLength, $totalLength, $symbol = '-') {

   // USAGE:
   $uid = \Edev\Resource\Uid::create('US', 5, 20);
   echo $uid; // output: `USDD9DC-26C02-5F313`
```

### Token

Used to generate tokens for employee verification

### Templater

Used to template strings that contain bracketed variables with an array of data

## Repository

Repositories were removed in favor of the ORM layer

## Throwable

This namespace contains all extended \Exception classes. Both the namespace and this documenation need further expansion.

## Middleware

Currently only used for the AuthenticationMiddleware.php. Creating user sessions and log-in/log-out.

## Route

Contains (3) files, **RouteController.php**, **RouteParser.php**, **Routes.php**. RouteParser class captures the URI and HTTP VERB, then RouteController::submit() takes them the uri and the parsed data as arguments to create the Controller child.

## Controller

The Controller method contains some protected methods that are available to all child instantiations.

##### ::redirect

```php
  protected function redirect($url = null) {
```

## Interface
