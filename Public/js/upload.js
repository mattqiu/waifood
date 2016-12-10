var url = "http://"+window.location.host+"/"; //图片上传回调地址
function upload(){};
upload.prototype.uploadPic= function(scope,uploadPhp){
    uploadPhp = url+'/public/uploadForKindeditor.php';
    var uploadJson = uploadPhp+"?scope="+scope+"&callback="+url+"Public/redirect.html";
    var editor = KindEditor.editor({
        uploadJson : uploadJson
    });
    editor.loadPlugin('image', function() {
        editor.plugin.imageDialog({
            showRemote:false,
            clickFn : function(url,title){

                var path = url.replace(/\/Public/, "");
                upload.prototype.callback(url, path);
                setTimeout(function() {
                    editor.hideDialog().focus();
                }, 0);
            }
        });
    });
}

upload.prototype.createContent = function(contentId, scope){
    uploadPhp = 'http://www.lingdianit.com/uploadForKindeditor.php';
    var uploadJson = uploadPhp+"?scope="+scope+"&from=3cfoodcn&callback="+url+"Public/redirect.html";
    var options ={
        filterMode:false,
        showRemote : false,
        width:'700px',
        height:'350px',
        items : ['source','fontname','fontsize','forecolor','preview','selectall','justifyleft','justifycenter','justifyright','emoticons','link','unlink','image'],
        uploadJson :uploadJson,
        afterChange : function() {
            this.sync();
        },
        afterBlur:function(){
            this.sync();
        }
    };
    var editor = KindEditor.create("#"+contentId,options);
    return editor;
}


/*前端验证上传文件
    op验证对象
    obj要验证的上传文件或图片input 元素 可以为数组
    filesize 验证上传图片的大小 b为单位
    type 验证的上传文件格式 array 类型
    w 验证上传图片的宽度px
    h验证上传图片的高度px
    callback //只有上传的图片才会返回
 */
upload.prototype.uploadCheck=function(op){
    $(op.obj).each(function(){
        if(!op.fileSize){
            op.fileSize=2*1024*1024;
        }
        $(this).prop('uploadFile',1).change(function(event){
            var t=this;
            var file=event.target.files[0];
            if(this.length>1){
                clearpop('只能选择一个文件');
                $(t).prop('uploadFile',1);
            }else{
                var error='';
                //验证文件类型
                if(op.type){
                    var filetype='';
                    //如果js没有获取到文件类型
                    if(!file.type || file.type==''){
                        var filetype = file.name.substr(file.name.lastIndexOf('.')+1);
                    }else{
                        var type=file.type.split('/');
                        if(type[0]=='image'){
                            filetype=type[1];
                        }else{
                            filetype=type[0];
                        }
                    }
                    var x=false;
                    for(var i=0;i<op.type.length;i++){
                        if(op.type[i]==filetype){
                            x=true;
                            break;
                        }
                    }
                    error+=x?'':'请上传'+op.type.join(',')+'格式文件,';
                }
                //验证文件大小
                if(op.fileSize){
                    if(file.size>op.fileSize){
                        error=error+"上传文件不得超过"+op.fileSize+'B,';
                    }
                }
                t.callback=function(error){
                    if(error!=''){
                        clearpop(error);
                        $(t).prop('uploadFile',1);
                    }else{
                        $(t).prop('uploadFile',0);
                    }
                }

                var reader = new FileReader();
                // onload是异步操作
                reader.onload = function(e){
                    var img=document.createElement('img');
                    img.src=e.target.result;
                    //验证上传文件的宽度 只针对于图片
                    if((op.w || op.h) && filetype=='image'){
                        var h='x';
                        var w='x';
                        if(op.w && op.w!=img.naturalWidth){
                            w=op.w;
                        }
                        if(op.h && op.h!=img.naturalHeight){
                            h=op.h
                        }
                        if(op.w!=img.naturalWidth || op.h!=img.naturalHeight){
                            error+='请上传'+w+'*'+h+'大小的图片,';
                        }
                        t.callback(error);
                    }else{
                        t.callback(error);
                    }
                    if(typeof op.callback=='function'){
                        op.callback(img.src,t,error);
                    }
                }
                reader.readAsDataURL(file);


            }
        });
    });
}
//自定义上传文件
//url 上传的地址
//obj上传的input只能设置一个 callback 上传成功后的回调
//type 必需 上传的类型用于后台提示属于那种提交
upload.prototype.uploadFile=function(op){
    var _op={
        url:'',//上传的地址
        obj:false,//上传的对象
        type:false,//上传的类型
        callback:false//回调函数
    }
    op=$.extend(_op,op);
    if($(op.obj)[0].files.length==0){
        clearpop('还未选择上传文件');
        return false;
    }
   if($(op.obj).prop('uploadFile')) {
       clearpop('请先上传标准文件。');
   }else{
       //进度条设置
       var progress = $('#progress');
       if (progress.length == 0) {
           progress = document.createElement('input');
           progress = $(progress).attr({id: 'progress', type: 'range', max: 100, min: 1});
           progress.appendTo($('body'));
       }
       //设置上传
       var fd = new FormData();
       //请求的参数
       fd.append("file", $(op.obj)[0].files[0]);
       console.log(fd);
      // op.url='/home/index/index';
       $.ajax({
           url: op.url,
           type: 'POST',
           data: fd,
           processData: false,//用来回避jquery对formdata的默认序列化，XMLHttpRequest会对其进行正确处理
           contentType: false,//设为false才会获得正确的conten-Type
           async: true,
           success: function (data) {
               if (data.code == 100) {
                   if (op.callback && typeof op.callback == 'function') {
                       op.callback(data.data);
                   }
               } else {
                   clearpop(data.message);
               }
           }
       })
   }
}