<?php

/**
 * image
 *
 * @author: william <377658@qq.com>
 * @copyright: Copyright (c) 2012 UFCEC Tech All Rights Reserved.
 * @version: $Id:image.php  2012年11月11日 星期日 15时55分41秒Z $
 */
namespace Import;

use Config;

class Transduce_Image {
    
    public function result($attribute, &$value, $parameters) {
        $split = $parameters[0];
        if(!$split) return false;

        $value = rtrim($value, $split);
        if($value) {
            $images = explode($split, $value);
        }

        foreach($images as $image) {
            $imagepath = \UploadHelper::path(path('product_image'), $image, true);
            if(! file_exists($imagepath) ) {
                $column = Config::get('import::import.maps')[$attribute];
                $message = sprintf(__('import::import.image_is_not_exists'), $column, $image);
                $row = sprintf(__('import::import.current_row'), Config::get('import::import.current_row'));
                Throw new ImportRowException($row.$message);
                return false;
            }
        }

        $value = $images;

        return true;
    }
}

?>
