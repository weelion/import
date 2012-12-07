<?php

/**
 * 验证
 *
 * @author: william <377658@qq.com>
 * @copyright: Copyright (c) 2012 UFCEC Tech All Rights Reserved.
 * @version: $Id:validator.php  2012年11月11日 星期日 00时23分07秒Z $
 */
namespace Import;

use Config;

class Validator extends \Validator {

    /**
     * 验证是否转换成功
     *
     * @param: $attribute  string 字段名称
     * @param: &$value     string 值引用
     * @param: $parameters array  参数表名字段等数据
     *
     * return bool
     */
    protected function validate_transduce($attribute, $value, $parameters) {

        $model = array_shift($parameters);
        $class = 'Import\Transduce_'.ucfirst($model);
        $transpose = new $class();
        $result = $transpose->result($attribute, $value, $parameters);
        Config::set('import::import.transduce.'.$attribute, $value);

        return $result;
    }
}

?>
