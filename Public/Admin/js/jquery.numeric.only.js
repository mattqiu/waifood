$.fn.numeric = function() {   
	$(this).css("ime-mode", "disabled");   
	this.bind("keypress",function(e) {  
	var code = (e.keyCode ? e.keyCode : e.which);  //兼容火狐 IE  
		 if (e.keyCode == 45) {
            this.value = this.value.replace('-', '');
            this.value = '-' + this.value;
            return false;

        } else if(!(code==9||code==46)){
		if(!$.browser.msie&&(e.keyCode==0x8))  //火狐下 不能使用退格键   
		{   
		 return ;   
		}   
		return code >= 48 && code<= 57;   
		}
		  
	});   
	this.bind("blur", function() {   
		if (this.value.lastIndexOf(".") == (this.value.length - 1)) {   
			this.value = this.value.substr(0, this.value.length - 1);   
		} else if (isNaN(this.value)) {   
			this.value = "";   
		}   
	});   
	this.bind("paste", function() {   
		var s = clipboardData.getData('text');   
		if (!/\D/.test(s));   
		//value = s.replace(/^0*/, '');   
		return false;   
	});   
	this.bind("dragenter", function() {   
		return false;   
	});   
	this.bind("keyup", function() {   
	if (/(^0+)/.test(this.value)) {   
		//this.value = this.value.replace(/^0*/, '');   
		}   
	});   
};   


$(function() {
    $(".numeric").numeric();

});