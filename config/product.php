<?php

/**
 * 产品导入配置信息
 */
return [

    /*
    | -----------------------------------------------------------------------
    | 导入设置
    | -----------------------------------------------------------------------
    | 数据库字段名与表格列名映射
    | 顺序要跟模板表格对应的列保持一致
    */
    'import' => [
        'name'             => ['name' => '名称',     'rule' => 'max:255'],
        'sku'              => ['name' => 'SKU',      'rule' => 'max:20'], 
        'language'         => ['name' => '语言',     'rule' => 'in:cn,en,de'],
        'category_id'      => ['name' => '分类',     'rule' => 'max:60|transduce:category,name,integer'],
        'cost'             => ['name' => '成本价',   'rule' => 'match:/^\d+\.\d{2}$/'],
        'min_price'        => ['name' => '最低价格', 'rule' => 'match:/^\d+\.\d{2}$/'],
        'max_price'        => ['name' => '最高价格', 'rule' => 'match:/^\d+\.\d{2}$/'],
        'supplier_id'      => ['name' => '供应商',   'rule' => 'max:100|transduce:table,suppliers,company,integer'],
        'devel_id'         => ['name' => '开发人',   'rule' => 'max:200|transduce:table,users,username,integer'],
        'weight'           => ['name' => '重量',     'rule' => ''],
        'size'             => ['name' => '尺寸',     'rule' => ''],
        'images'           => ['name' => '图片',     'rule' => 'match:/^(\w+\_\d+\.(jpg)\;)+$/|transduce:array,;,array'],
        'keywords'         => ['name' => '关键词',   'rule' => 'max:255'],
        'short_descrption' => ['name' => '简要描述', 'rule' => ''],
        'description'      => ['name' => '详细描述', 'rule' => ''],
    ],

    /*
    | -----------------------------------------------------------------------
    | 场景规则
    | -----------------------------------------------------------------------
    | 1.如果没有必填限制
    | <code>
    |     'scene' => [],
    | </code>
    | 
    | 2.默认场景
    | <code>
    |     'scene' => ['name', 'sku', 'language'],
    | </code>
    */
    'scenes' => [
        'case'      => 'language',
        'requires' => [
            'cn' => [
                'name', 'sku', 'category_id', 'cost', 'min_price', 'max_price',
                'supplier_id', 'devel_id', 'images', 'description',
                // 'weight', 'size', 
            ],
            'default' => [
                'name', 'sku', 'keywords', 'short_descrption', 'description',
            ]
        ],
        ],

    // 存储
    'storage' => [
        'products' => ['sku', 'cost', 'category_id', 'supplier_id', 'devel_id', 'min_price', 'max_price', 'weight', 'size', 'status' => 1],
        'porducts_extensions' => ['product_id', 'language', 'name', 'description', 'keywords', 'short_descrption', 'created_at' => 'datetime'],
        'proudcts_images' => ['product_id', 'images', 'created_at' => 'datetime'],
    ],


];
