<?php
namespace Shop\Controller;
use Common\Model\CodeModel;
use Think\Controller;

class ImagesController extends Controller{
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
//压缩
//                if (($this->config['fixedWidth'] > 0) ||($this->config['fixedHeight'] > 0)) {//缩略图
//                    $filename=$savePath.'t_'.substr(md5($stream), 8, 16) . ".{$type}";
//                    $new_file=self::UPLOAD_PATH.$filename;
//                    if(!$this->img2thumb($src_img, $new_file, $width = $this->config['fixedWidth'], $height = $this->config['fixedHeight'])){
//                        return false;
//                    }
//                    unlink($src_img) ;//删除源文件
//                }
//                //判断是否是个文件
//                $info = getimagesize($new_file);

            }else{
                apiReturn(CodeModel::ERROR,'图片保存失败'.$path.$savePath);
            }
        }else{
            apiReturn(CodeModel::ERROR,'图片流上传失败');
        }
    }
}
?>