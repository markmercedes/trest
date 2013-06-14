<?php

class StackOverflowUser extends TRestModel {
    
    protected static $configName = 'StackOverflow';
    
    public static $listItemNode = 'users';
    
    public static $singleItemNode = 'users';
    
    public static $resource = 'users';

    public function fields() {
        return array(
            'user_id' => array(
                'type' => 'integer'
            ),
            'user_type' => array(
                'type' => 'string'
            ),
            'location' => array(
                'type' => 'string'
            ),
            'website_url' => array(
                'type' => 'string'
            ),
            'display_name' => array(
                'type' => 'string'
            ),
            'reputation' => array(
                'type' => 'integer'
            ),
            'age' => array(
                'type' => 'integer'
            ),
            'about_me' => array(
                'type' => 'string'
            ),
            'last_access_date' => array(
                'type' => 'DateTime',
                'format' => 'UNIX'
            )
        );
    }

    public function relations() {
        return array(
            'badge_counts' => array(
                'class' => 'StackOverflowUserBadge',
                'type' => self::HAS_ONE
            )
        );
    }
}