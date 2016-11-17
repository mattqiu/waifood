function logout(id) {
    var title = "Are you sure you want to quit?";
    jConfirm(title,SYSTITLE,function(msg){
        if(msg){
            var url = CONST_CART.replace('Cart/URL', 'Login/logout');
            location = url;
        }else{
            return false;
        };
    });
    return false;
};