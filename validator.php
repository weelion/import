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
use DB;

class Validator extends Laravel\Validator {

    /**
     * 验证是否转换成功
     *
     * @param: $attribute  属性名称
     * @param: &$value     值引用
     * @param: $parameters 参数表名字段等数据
     *
     * return bool
     */
    public function validate_transduce($attribute, &$value, $parameters) {
    
    }

}

?>
