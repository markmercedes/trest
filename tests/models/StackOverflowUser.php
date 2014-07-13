<?php

use TRest\Models\Model;

class StackOverflowUser extends Model {
    
    protected static $configName = 'StackOverflow';
    
    public static $listItemNode = 'items';
    
    public static $singleItemNode = 'items';
    
    public static $resource = 'users';
    
    public static function findOne($id){
        return parent::find()->fromSO()->findOne($id);
    }
    
    public static function find(){
        return parent::find()->fromSO();
    }
    
    public function scopeFromSO($query){
        return $query->where('site', 'stackoverflow');
    }

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