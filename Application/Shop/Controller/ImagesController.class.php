<?php

class ImagesController{

    public function upload($config){
        $savePath =$config['savepath']?$config['savepath']:'/upload/other/';
        $hostname = php_uname('n');
        if (strtolower($hostname) == 'pc-20160819tbmr') { // 本地模式
            $absSavePath = 'D:/WAMP/waifood/Public/upload/'.$savePath;
        } else {
            $absSavePath = '/home/www/waifood/Public/upload/'.$savePath;
        }
        if (!is_dir($absSavePath)) {
            if (!mkdir($absSavePath, 0777, true)) {
                GLog('upload', '上传目录' . $absSavePath . '无法创建');
                return false;
            }
        }
        $fileInfo = array();
        $files = $this->dealFiles($_FILES);
        foreach ($files as $key => $file) {
            if (empty($file['name'])) {
                continue;
            }
            //登记上传文件的扩展信息
            $file['key'] = $key;
            $pathinfo = pathinfo($file['name']);//取得上传文件的后缀
            $file['extension'] =$pathinfo['extension'] ;
            $file['savepath'] = $savePath;
            $file['abssavepath'] = $absSavePath;
            // 使用子目录保存文件
            $file['savename'] = date('Ymd', time()) . '/'.  md5($file['name']) . "." . $file['extension'];
            $file['filepath'] = '/'.$savePath . $file['savename'];
            if (!$this->check($file,$config)) {
                return false;
            }
            //保存上传文件
            if (!$this->save($file)) {
                return false;
            }
            $fileInfo[] =  $file['filepath'];
        }
        return $fileInfo;
    }

    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files 上传的文件变量
     * @return array
     */
    public function dealFiles($files) {
        $fileArray = array();
        $n = 0;
        foreach ($files as $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $key)
                        $fileArray[$n][$key] = $file[$key][$i];
                    $n++;
                }
            } else {
                $fileArray[$n] = $file;
                $n++;
            }
        }
        return $fileArray;
    }

    /**
     * 检查上传的文件
     * @access private
     * @param array $file 文件信息
     * @return boolean
     */
    public function check($file,$config) {
        // 如果是图像文件 检测文件格式
        if (in_array(strtolower($file['extension']), array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'))) {
            $imgInfo = getimagesize($file['tmp_name']);
            if (false === $imgInfo) {
                return '非法图像文件';
            }
            if(isset($config['maxWidth']) || isset($config['maxHeight'])){
                if (($config['maxWidth'] > 0 && $imgInfo[0] > $config['maxWidth']) ||
                    ($config['maxHeight'] > 0 && $imgInfo[1] > $config['maxHeight'])) {
                    return "图片长宽不能超过{$config['maxWidth']}*{$config['maxHeight']}";
                }
            }
            if(isset($config['fixedWidth']) || isset($config['fixedHeight'])) {
                if (($config['fixedWidth'] > 0 && $imgInfo[0] != $config['fixedWidth']) ||
                    ($config['fixedHeight'] > 0 && $imgInfo[1] != $config['fixedHeight'])
                ) {
                    return "图片长宽需为 {$config['fixedWidth']}*{$config['fixedHeight']}";
                }
            }
        }
        if ($file['error'] !== 0) {
            //文件上传失败
            //捕获错误代码
            return ($file['error']);
        }
        //文件上传成功，进行自定义规则检查
        //检查文件大小
        if(isset($config['maxSize'])){
            if ($config['maxSize'] > 0 && $file['size'] > $config['maxSize']) {
                return  '上传文件最大为'.$config['maxSize'].'B！';
            }
        }

        //检查文件Mime类型
        if (!empty($config['allowTypes']) && !in_array(strtolower($file['type']), $config['allowTypes'])) {
            return '上传文件MIME类型不允许！';
        }

        //检查文件类型
        if (!empty($config['allowExts']) && !in_array(strtolower($file['extension']), $config['allowExts'], true)) {
            return '上传文件类型不允许';
        }

        //检查是否合法上传
        if (!is_uploaded_file($file['tmp_name'])) {
            return '非法上传文件！';
        }
        return true;
    }

    /**
     * 上传一个文件
     * @access public
     * @param mixed $name 数据
     * @param string $value 数据表名
     * @return string
     */
    public function save($file) {
        $filename = $file['abssavepath'] . $file['savename'];
        if (is_file($filename)) {
            // 不覆盖同名文件
            return  '文件已经存在！' . $filename;
        }

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            return '文件上传保存错误！';
        }
        return true;
    }



    /**
     * base64上传
     */
    public function createimg(){
        $stream = I('post.stream');
        $path = C('UPLOAD_PATH');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $stream, $result)) {
            $type = $result[2];
            $savePath =  'upload/'.date('Y-m-d').'/';
            $mdpath =$path.$savePath;
            if(!is_dir($mdpath)){
                mkdir($mdpath, 0777);
            }
            $filename =$savePath.substr(md5($stream), 8, 16) . ".{$type}";
            if (file_put_contents($path.$filename,base64_decode(substr(strstr($stream,','),1)))) {
                $info = getimagesize($path.$filename);
                //判断是否是个文件
                if($info){
                    apiReturn(CodeModel::ERROR,'图片上传成功',$filename);
                }else{
                    apiReturn(CodeModel::ERROR,'图片上传失败');
                }
            }else{
                apiReturn(CodeModel::ERROR,'图片保存失败'.$path.$savePath);
            }
        }else{
            apiReturn(CodeModel::ERROR,'图片流上传失败');
        }
    }
}
?>