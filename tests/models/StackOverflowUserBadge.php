<?php

use TRest\Models\Model;

class StackOverflowUserBadge extends Model {

    public function fields() {
        return array(
            'gold' => array(
                'type' => 'integer'
            ),
            'silver' => array(
                'type' => 'integer'
            ),
            'bronze' => array(
                'type' => 'integer'
            )
        );
    }

    public function relations() {
        return array(
            'user' => array(
                'class' => 'StackOverflowUser',
                'type' => self::BELONGS_TO
            )
        ); 
    }
}

?>