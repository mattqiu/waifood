<?php
function getCityByCity($cityname){
    switch($cityname){
        case 'chengdu':return '成都';break;
        case 'chongqing':return '重庆';break;
        case "xi'an":return "西安";break;
        case 'kunming':return '昆明';break;
        case 'other':return '其他';break;
    }
}
?>