<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "goods";
		return false;
    });

	var button2 = $('#upload_btn2'), interval;
    new AjaxUpload('#upload_btn2', {
		action: rootUri + 'uploadimage/upload',
		onSubmit : function(file , ext){
			$('#loading_photo2').show();
			if (! (ext && /^(JPG|PNG|JPEG|GIF)$/.test( ext.toUpperCase() ))){
				// extensiones permitidas
				alert('Error: Only image files please','');        
				$('#loading_photo1').hide();
				return false;
			} 
		},
		onComplete: function(file, response){
			var resize_url = rootUri + 'uploadimage/resize';        
			var f_name = response;
			$.ajax({
				url: resize_url,
				data: {
					"photo": f_name,
					"kinds" : "<?php echo implode(',', array(COMPANY_IMAGE_PREFIX)); ?>"
				},
				type: "post",
				success: function(message){
					$('#loading_photo2').hide();


                    var i = 0;
                    for(i = 0; i < 5; i++)
                    {
                        if ($('#imguri_' + i).attr('src') == rootUri + "www/images/u42_normal.png")
                        {
                            $('#imguri_' + i).attr('src', rootUri + "www/images/uploads/products/image/" + f_name);
                            break;
                        }
                    }

                    var imguri = $('#imguri').val();
                    var arrayOfimguri = imguri.split(',');
                    arrayOfimguri[i] = f_name;
                    imguri = "";
                    for(var j = 0; j < 5; j++)
                    {
                        if (imguri == '')
                            imguri = arrayOfimguri[j];
                        else
                            imguri = imguri + "," + arrayOfimguri[j];
                    }

                    $('#imguri').val(imguri);


                    /*
					var pic_num = $("#img2").find('div').size();
					var pic_data = "<div style='float:left; padding:5px;'>";
                    pic_data += "<img src='" + rootUri + "/www/images/uploads/products/image/" + f_name+ "' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
                    pic_data +=  "<img src='" + rootUri + "www/images/image_close.png' class='close_btn' onclick='removeMe2(this, \""+f_name+"\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                    pic_data += "</div>";
					$('#img2').html( $('#img2').html()+pic_data );
					if( $('#img_name2').val() == "" )
						$('#img_name2').val( f_name );
					else
						$('#img_name2').val( $('#img_name2').val() + "," + f_name);
					*/
				}
			}); 
		}
    });
});

function over_img(obj)
{
	clearTimeout(timeoutID);
	timer_flag = false;
	if(img_parent_div)
		$(img_parent_div).find(".close_btn").css('visibility', 'hidden');
	var obj_parent = $(obj).parent();    
	//$(obj_parent).find(".close_btn").show(); 
	$(obj_parent).find(".close_btn").css('visibility', 'visible');
}
var img_parent_div = null;
var timeoutID;
function out_img(obj)
{   
    img_parent_div = $(obj).parent(); 
    timeoutID = setTimeout("timerProc( )", 1000);
    timer_flag = true;
}
var timer_flag = false;
var close_flag = false;
function timerProc()
{
    if(!close_flag)
    {
        $(img_parent_div).find(".close_btn").css('visibility', 'hidden');
    }
    timer_flag = false;
}
function over_close(obj)
{
   close_flag = true; 
}
function out_close(obj)
{
    close_flag = false;
    
    if(!timer_flag)
        $(obj).css('visibility', 'hidden');
}

function removeMe2(obj, f_name, index)
{
	var pic_div = obj.parentNode;
    var imgobj = $('#imguri_'+index);
    var url;
    url = rootUri + "company/removephoto";
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) { 
        	//$(pic_div).remove();
            imgobj.attr('src', rootUri + 'www/images/u42_normal.png');
			var img_name_ary = $('#imguri').val().split(',');
			var img_name_data = "";
			for( var i=0; i < img_name_ary.length; i++ ) {
				if( i == index )
					img_name_data += "" + ",";
				else
					img_name_data += img_name_ary[i] + ",";
			}
			img_name_data = img_name_data.substr(0, img_name_data.length-1);
			$('#imguri').val( img_name_data );
        }
    });        
}

//]]>
</script>