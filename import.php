<?php
/**
 * Excel表格导入处理
 *
 * @author: william <377658@qq.com>
 * @copyright: Copyright (c) 2012 UFCEC Tech All Rights Reserved.
 * @version: $Id:import.php  2012年11月08日 星期四 16时44分29秒Z $
 */
namespace Import;

use Config;
use Lang;
use PHPExcel;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;

class ImportException extends \Exception {}
class ImportFileException extends \Exception {}
class ImportRowException extends \Exception{}

class Import {

    /**
     * 导入的数据
     */
    private $_data = [];

    /**
     * 导入表格头
     */
    private $_headers = [];

    /**
     * 导入表字段
     */
    private $_fields = [];

    /**
     * 场景设置
     */
    private $_scenes = [];

    /**
     * 验证规则
     */
    private $_rules = [];

    /**
     * 验证错误提示信息
     */
    private $_messages = [];

    /**
     * 当场景中有case才使用
     */
    private $_index = null;

    /**
     * 导入构造函数
     *
     * @param: $config   string 导入配置名称
     * @param: $filepath string 上传文件绝对路径
     *
     */
    public function __construct($config, $filepath) {
        $this->_initConfig($config);
        $this->_initData($filepath);
        $this->_initScenes();
        $this->_valid();
    }

    /**
     * 返回数据
     *
     * return array
     */
    public function data() {
        return ;
    }

    
    /**
     * 初始化配置
     *
     * @param: $config string 配置名称
     *
     */
    private function _initConfig($config) {
        // 配置
        if( $configs = Config::get('import::'.$config) ) {
            // 导入设置
            if(isset($configs['import'])) {
                $this->_fields = array_keys($configs['import']); // 数据对应字段
                $rules = [];
                foreach($configs['import'] as $config) {
                    $rules[] = $config['rule'];
                    $this->_headers[] = $config['name'];  // 表格头部标题
                }

                $this->_rules = array_combine($this->_fields, $rules);  // 验证规则

            } else {
                $this->_exception(__('import::import.no_import_setting'));
            }

            // 场景设置
            if(isset($configs['scenes']) && is_array($configs['scenes'])) {
                $this->_scenes = $configs['scenes'];
            } else {
                $this->_exception(__('import::import.no_scenes_setting'));
            }

            // 保存设置
            if(isset($configs['storage']) && is_array($configs['storage'])) {
                $this->_storage = $configs['storage'];
            } else {
                $this->_exception(__('import::import.no_storage_setting'));
            }

        } else {
            $message = sprintf(__('import::import.no_config'), $config);
            $this->_exception($message);
        }
    }

    /**
     * 数据初始化
     *
     * @param: $filepath string 路径
     *
     */
    private function _initData($filepath) {
        // 读取文件
        $PHPExcel = new PHPExcel();
        $PHPRead = new PHPExcel_Reader_Excel2007();
        if( !$PHPRead->canRead($filepath) ) {
            $PHPRead = new PHPExcel_Reader_Excel5();
            if( !$PHPRead->canRead($filepath) ) {
                $this->_fileException(__('import::import.cant_read_the_import_file'));
            }
        }
        $PHPExcel = $PHPRead->load($filepath);
        $data = $PHPExcel->getSheet(0)->toArray();

        array_shift($data);     // 删除标题行数据

        $this->_data = $this->_trim($data);
    }

    /**
     * 初始化数据验证场景
     *
     */
    private function _initScenes() {
        // 场景设置
        if( isset($this->_scenes['case'] ) && $case = $this->_scenes['case']) {
            $this->_rules[$case] = $this->_require($this->_rules[$case]);
            $this->_index = array_search($case, $this->_fields);
        } elseif(!empty($this->_scenes)) {
            foreach($this->_scenes as $scenes) {
                $this->_rules[$scene] = $this->_require($rules[$scenes]);
            }
        }

    
    }

    /**
     * 添加必须的验证属性
     *
     * return string
     */
    private function _require($rule) {
        $require = ['required|', 'required'];
        $rule = str_replace($require, '', $rule);
        $rule = empty($rule) ? 'required' : 'required|'.$rule;

        return $rule;
    }

    /**
     * 验证
     *
     */
    private function _valid() {

        // 表头验证
        $headers = array_shift($this->_data);
        if($headers != $this->_headers) $this->_exception(__('import::import.not_a_standard_file'));

        // 数据验证
        $this->_messages = Config::get('import::error'); // 验证提示
        foreach($this->_data as $index => $data) {

            $current_row = $index + 2;

            // 验证场景
            if($this->_index) {
                if(in_array($data[$this->_index], array_keys($this->_scenes['requires']))) {
                    $requires = $this->_scenes['requires'][$data[$this->_index]];
                } else {
                    $requires = $this->_scenes['requires']['default'];
                }
            } else {
                $requires = $this->_scenes;
            }

            foreach ($requires as $require) {
                if( isset($this->_rules[$require]) ) {
                    $this->_rules[$require] = $this->_require($this->_rules[$require]);
                } else {
                    $this->_exception(sprintf('import::import.not_have_this_scene'), $require);
                }
            }

            // 数据
            $data = array_combine($this->_fields, $data);
            if(! $data) $this->_rowException($current_row, __('import::import.data_length_not_match'));
            
            $valid = Validator::make($data, $this->_rules, $this->_messages);
            if( $valid->fails() ) {
                $messages = str_replace( ' ', '_', $valid->errors->first() );
                $this->_rowException( $current_row, str_replace($this->_fields, $this->_headers, $messages) );
            } else {
                // 存储
            }
            die;

        }
        die;
    }

    /**
     * 过滤空数据
     *
     *
     *
     */
    private function _trim($data) {
        $count = count($this->_fields);
        foreach($data as $index => $datum) {
            $i = 0;
            foreach($datum as $idx => $value) {
                $value = trim($value);
                if(empty($value)) $i++;
                $data[$index][$idx] = $value;
            }

            // 删除全空的行
            if($i == $count) unset($data[$index]);
        }

        return $data;
    }

    

    /**
     * 抛出导入包异常
     *
     * @param: $message string 消息
     *
     * @throws: ImportException
     */
    private function _exception($message) {
        Throw new ImportException($message);
        
    }

    /**
     * 抛出导入操作异常
     *
     * @param: $message string 消息
     *
     * @throws: ImportException
     */
    private function _fileException($message) {
        Throw new ImportFileException($message);
    }

    /**
     * 抛出导入文件行异常
     *
     * @param: $row     integer 当前行号
     * @param: $message string  信息
     *
     * @throws: ImportRowException
     */
    private function _rowException($row, $message) {
        $row = sprintf(__('cerrent_row'), $row); 
        Throw new ImportRowException($row.$message);
    }
}


?>
