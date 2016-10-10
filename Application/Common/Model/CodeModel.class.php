<?php
namespace Common\Model;
use Think\Model;

class CodeModel extends Model
{
    /* 常用 请求返回码*/
    const CORRECT = 200;      //正常返回
    const ERROR =  400;       //错误返回

    public static function getMessage($code){
        switch($code){
            case self::CORRECT:  return "success";
            case self::ERROR:  return "error";
            default: return "An unknown error";
        }
    }

}