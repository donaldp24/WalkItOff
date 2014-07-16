<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "goodsbanner";
		return false;
    })

	$(".chosen-select").chosen(); 

	
	$('#showorder').ace_spinner({value:<?php if(isset($info['showorder'])) echo $info['showorder']; else echo '1'; ?>,min:1,max:999999999,step:1, icon_up:'icon-caret-up', icon_down:'icon-caret-down'});

    var button1 = $('#upload_btn1'), interval;
    new AjaxUpload('#upload_btn1', {
		action: rootUri + 'uploadimage/upload',
		onSubmit : function(file , ext){
			$('#loading_photo1').show();
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
					"kinds" : "<?php echo implode(',', array(BANNER_IMAGE_PREFIX)); ?>"
				},
				type: "post",
				success: function(message){                
					$('#loading_photo1').hide();
						  
					var str_html = "<img src='" + rootUri + "www/images/uploads/products/image/" + f_name+ "' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
					str_html +=  "<img src='" + rootUri + "www/images/image_close.png' class='close_btn' onclick='removeMe1(\""+f_name+"\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
					$('#img1').html(str_html);    
					$('#img_name1').val(f_name);
				}
			}); 
		}  
    });	
	
	$.validator.messages.required = "必须要填写";

	$('#validation-form').validate({
		errorElement: 'span',
		errorClass: 'help-inline',
		focusInvalid: false,
		rules: {
			name: {
				required: true
			},
			goodsno: {
				required: true,
				minlength: 5
			},
			order_price: {
				required: true,
				order_price: 'required'
			},
			price: {
				required: true,
				price: 'required'
			},
			pattern: 'required',
			showorder : 'required'
		},

		messages: {
			goodsno: {
				minlength: "请输入至少5个数字"
			},
			pattern: "请选择款式"
		},

		invalidHandler: function (event, validator) { //display error alert on form submit   
			$('.alert-error', $('.login-form')).show();
		},

		highlight: function (e) {
			$(e).closest('.control-group').removeClass('info').addClass('error');
		},

		success: function (e) {
			$(e).closest('.control-group').removeClass('error').addClass('info');
			$(e).remove();
		},

		errorPlacement: function (error, element) {
			if(element.is(':checkbox') || element.is(':radio')) {
				var controls = element.closest('.controls');
				if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
				else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
			}
			else if(element.is('.select2')) {
				error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
			}
			else if(element.is('.chosen-select')) {
				error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
			}
			else error.insertAfter(element);
		},

		submitHandler: function (form) {

			if(!form.valid()) return false;

			alert("WWW");
			$('#check_save').attr("value", "submit");

			form.submit();
		},
		invalidHandler: function (form) {
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
function removeMe1(f_name)
{
    var url;
    url = rootUri + "goodsbanner_add/remove_photo1";    
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) {
            $('#img1').html("");
			$('#img_name1').val('');
        }
    });        
}

function isValidDecimal(value)
{
//	var regexTest = /^\d{0,8}(\.\d{0,2})?$/;
	var regexTest = /^\d+(?:\.\d\d?)?$/;
	var ok = regexTest.test(value);
	return ok;
}

//]]>
</script>