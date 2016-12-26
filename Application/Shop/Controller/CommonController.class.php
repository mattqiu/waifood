<?php
namespace Shop\Controller;
use Common\Model\CodeModel;
use Think\Controller;
class CommonController extends Controller {

    public function ajaxDelfile(){
        $img = trim(I('post.file'));
        if(!empty($img)){
            if(delfile($img)){
                apiReturn(CodeModel::CORRECT);
            }
            apiReturn(CodeModel::ERROR);
        }
        apiReturn(CodeModel::ERROR);
    }
}
?>