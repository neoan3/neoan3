<?php
class index_model {
    /**
     * @param $model
     * @param $table
     * @param $id
     * @param array $param
     * @return array|int
     */
    static function undeleted($model,$table,$id, $param=[]){
        $arg = [$model . '_id' => $id, 'delete_date' => ''];
        if(!empty($param)){
            foreach($param as $key => $val){
               $arg[$key] = $val;
            }
        }
        return db::ask('?' . $model . '_' .$table, ['*'], $arg);
    }
    static function first($ask){
        if(!empty($ask)){
            return $ask[0];
        } else {
            return [];
        }
    }
}