<?php

class Ingredient extends TRestModel{
    
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
