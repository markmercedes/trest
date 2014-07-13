<?php

use TRest\Models\Model;

class Sandwich extends Model {

    protected static $resource = 'sandwiches';

    protected static $singleItemNode = 'sandwich';

    protected static $listItemNode = 'sandwiches';

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
