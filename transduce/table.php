<?php

/**
 * table
 *
 * @author: william <377658@qq.com>
 * @copyright: Copyright (c) 2012 UFCEC Tech All Rights Reserved.
 * @version: $Id:table.php  2012年11月11日 星期日 15时17分01秒Z $
 */
namespace Import;

use Config;
use DB;

class Transduce_Table {

    public function result($attribute, &$value, $parameters) {
    
        $query = DB::table($parameters[0]);
        if (is_array($value)) {
            $query = $query->where_in($parameters[1], $value);
        } else {
            $query = $query->where($parameters[1], '=', $value);
        }

        $id = $query->only('id');

        if(! empty($id) ) {
            $value = $id;
            return true;
        } else {
            $column = Config::get('import::import.maps')[$attribute];
            $message = sprintf(__('import::import.table_field_is_not_exists'), $column, $value);
            Throw new ImportRowException(Config::get('import::import.current_row'), $message); 
            return false;
        }
    }
}
?>
