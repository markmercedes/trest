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

    public function scopePage($query, $page){
        return $query->where('page', $page);
    }

    public function scopeLimit($query, $limit){
        return $query->where('pagesize', $limit);
    }

    public function scopeSortBy($query, $sortBy){
        return $query->where('sort', $sortBy);
    }

    public function scopeOrder($query, $order){
        return $query->where('order', $order);
    }

    public function fields() {
        return array(
            'user_id' => array(
                'type' => 'integer'
            ),
            'user_type' => array(
                'type' => 'string'
            ),
            'reputation' => array(
                'type' => 'integer'
            ),
            'display_name' => array(
                'type' => 'string'
            ),
            'reputation' => array(
                'type' => 'integer'
            ),
            'is_employee' => array(
                'type' => 'boolean'
            ),
            'link' => array(
                'type' => 'string'
            ),
            'profile_image' => array(
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