<?php
return array(
    'banner' => array(
        'savepath'    => "banner/",
        'allowExts'         =>  array("jpg","gif","png","jpeg"),
        'subType'           =>  'date',   // 子目录创建方式 可以使用hash date
        'dateFormat'        =>  'Ym',
        'uploadReplace'     =>  true,     // 存在同名是否覆盖
        'saveRule'          =>  'uniqid', // 上传文件命名规则
    ),
/*    "master_case"=>array(
        '0xiao_fixed_width' =>260,
        '0xiao_fixed_height'=>180,
        '0xiao_savepath'    =>"food/sharePic/",         //因需要对路径进行预处理，所有无需写UploadFile 类本身的save_path,程序会根据该字段自动生成
        'supportMulti'      =>  false,    // 是否支持多文件上传
        'maxSize'           =>  2097152,     // 上传文件的最大值 1024*1024 2MB
        'allowExts'         =>  array("jpg","gif","png","jpeg"),    // 允许上传的文件后缀 留空不作后缀检查
        'thumb'             =>  false,    // 使用对上传图片进行缩略图处理
        'autoSub'           =>  true,     // 启用子目录保存文件
        'subType'           =>  'date',   // 子目录创建方式 可以使用hash date
        'dateFormat'        =>  'Ym',
        'uploadReplace'     =>  true,     // 存在同名是否覆盖
        'saveRule'          =>  'uniqid', // 上传文件命名规则
        'hashType'          =>  '',       // 上传文件Hash规则函数名,不使用
    ),*/
    // 附件上传
/*    "file" => array(
        'save_project' => "crm",
        '0xiao_savepath' => "file/", //因需要对路径进行预处理，所有无需写UploadFile 类本身的save_path,程序会根据该字段自动生成
        'maxSize' => 10485760, // 上传文件的最大值  10MB
        'allowExts' => array("zip", "txt", "doc" ,"docx", "xls" ,'ppt' ,'pdf','rar',"jpg", "gif", "png", "jpeg"), // 允许上传的文件后缀 留空不作后缀检查
    ),*/

);