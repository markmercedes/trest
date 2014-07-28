# TRest

TRest is an ORM that maps REST resources to PHP objects. Designed to use in applications that need to consume data from a RESTful API.

## Usage

There is a sample file name trest_init.php which contains a default configuration which might help you understrand how you can initialize the library before start using it.


```php
<?php

/**
* You should allways define the constant `TREST_DEFAULT_CACHE_TTL`, this constant the default time that items will be     * cached in seconds, if you don't wanna catch your request define it and provide a value of 0/
*/
define('TREST_DEFAULT_CACHE_TTL', 120);

/**
 * The config named 'default' will be used for the models without the property
 * => protected static $configName = 'StackOverflow';
 */
ConfigFactory::add('default', new Config(array(
    'apiUrl' => 'http://pixelpt-sandwich-api.herokuapp.com/',
    'singleItemNode' => 'sandwich',
    /**
    * Here you can provide a cache adapter to your connection.
    *
    * To create your own cache adapters, implement the interface TRest\Cache\CacheAdapterInterface
    * and provide an instance of your class to the configuration key named cacheAdapter of your connection.
    *
    * Here should be passed an instance of a class that implements the interface TRest\Cache\CacheAdapterInterface.
    *
    */
    'cacheAdapter' => new ClassImplementingCacheAdapterInterface()
)));
```

## Introduction

The basic concept is simple, you have a REST service (http://pixelpt-sandwich-api.herokuapp.com/)
and you want to interact with it through a simple ActiveRecord style interface.

First we can retrieve a sandwich:

```php
$sandwich = Sandwich::findOne($id); // GET http://pixelpt-sandwich-api.herokuapp.com/sandwich/$id
```

Now we can change some properties of that sandwich:

```php
$sandwich->name = 'Double bacon cheese';
$sandwich->price = 9000;
```

Once we're done we can simply save it and the appropriate REST call will be made:

```php
$sandwich->save(); // PUT http://pixelpt-sandwich-api.herokuapp.com/sandwich/$id (title=Double bacon cheese, price=9000)
```

## Model

The REST client is an ActiveRecord style implementation for working with REST
services. All you need to do is define some PHP classes that are mapped to some
REST service on the web. Here is an example where we map a Sandwich to
http://pixelpt-sandwich-api.herokuapp.com/sandwich:

```php
<?php

namespace Entities;

use TRest\Models\Model;

class Sandwich extends Model
{

    /**
    * The name of the resource
    */
    protected static $resource = 'sandwiches';

    /**
    * In the case of this particular API, a single item in returned inside a node named with the resource
    * name singularized
    */
    protected static $singleItemNode = 'sandwich';
    /**
    * In the case of this particular API, a list of items in returned inside a node named with the resource
    * name pluralized
    */
    protected static $listItemNode = 'sandwiches';

    /*
    * Field definition
    */
    public function fields() {
        return array(
            'id' => array(
                'type' => 'integer'
            ),
            'title' => array(
                'type' => 'string'
            ),
            'price' => array(
                'type' => 'integer'
            )
        );
    }

    /**
    * Retlations
    */
    public function relations() {
        return array(
            'ingredients' => array(
                'class' => 'Ingredient',
                'type' => self::HAS_MANY,
                'postOnSave' => true,
                'postSuffix' => '_attributes'
            )
        );
    }
}

class Ingredient extends Model{

    protected static $resource = 'ingredients';

    protected static $singleItemNode = 'ingredient';

    protected static $listItemNode = 'ingredients';

    public function relations() {
        return array(
            'sandwich' => array(
                'class' => 'Sandwich',
                'type' => self::BELONGS_TO
            )
        );
    }

    public function fields() {
        return array(
            'id' => array(
                'type' => 'integer'
            ),
            'name' => array(
                'type' => 'string'
            ),
            'quantity' => array(
                'type' => 'integer'
            )
        );
    }

}
```

Now when we perform some actions it will generate the appropriate REST request,
execute it, transform the response and hydrate the results to your PHP objects.

```php
$sandwich = new Sandwich();
$sandwich->title = "mark";
$sandwich->price = 9200;
$sandwich->ingredients = [
    new Ingredient(
        (object)['name' => 'Bacon', 'quantity' => 2]
    ),
    new Ingredient(
        (object)['name' => 'Cheese', 'quantity' => 1]
    )
];
$sandwich->save();
/**
* POST http://pixelpt-sandwich-api.herokuapp.com/
* Results {"id":154,"title":"mark","price":9200,
* "ingredients":[{"id":315,"name":"Bacon","quantity":2},{"id":316,"name":"Cheese","quantity":1}]}
*/
```

Now you can continue working with your model and make more changes to it, also from that point on you will be able to retrive the ids of the models

```php
$sandwich->id; // => 154
$sandwich->ingrediends[0]->id; // => 315
```

We can retrieve that sandwich again now:

```php
$sandwich = Sandwich::findOne($sandwich->id); // GET http://pixelpt-sandwich-api.herokuapp.com/154
```

Or you can retrieve all Sandwich objects:

```php
$sandwiches = Sandwich::find()-all();
```


