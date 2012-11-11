<?php
/**
 *  这是一个excel辅助导入的包
 *
 * @author: william <377658@qq.com>
 * @copyright: Copyright (c) 2012 UFCEC Tech All Rights Reserved.
 * @version: $Id:start.php  2012年11月08日 星期四 16时41分06秒Z $
 */


// Autoload classes
Autoloader::namespaces(array(
    'Import' => Bundle::path('import'),
));

// Set the global alias for import 
Autoloader::alias('Import\\Import', 'Import');
Autoloader::alias('Import\\ImportException', 'ImportException');


