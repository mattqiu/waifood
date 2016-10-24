<?php
function getCityByCity($cityname){
    switch($cityname){
        case 'chengdu':return 'chengdu<br> 成都';break;
        case 'chongqing':return 'chongqing<br> 重庆';break;
        case 'chengdu':return "xi'an<br> 西安";break;
        case 'kunming':return 'kunming<br> 昆明';break;
        case 'other':return 'other<br> 其他';break;
    }
}
?>