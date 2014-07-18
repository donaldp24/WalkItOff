<script type="text/javascript">
//<![CDATA[

var set_flag = false;
var editor;
KindEditor.ready(function(K) {
		editor = K.create('#contents', { resizeType:1, langType:'zh_CN', width:'676px', height:'350px'});
});

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "message";
		return false;
    });

	$.validator.messages.required = "必须要填写";

	$('#validation-form').validate({
		errorElement: 'span',
		errorClass: 'help-inline',
		focusInvalid: false,
		rules: {
			name: required
            ,imguri: {
				required: true
			}
            ,linkurl: {
				required: true
			}
            ,showorder: {
                required: true
            }
        },

		messages: {
            linkurl: {
                email: "请输入店铺地址"
            }
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
function removeMe(f_name)
{
    var url;
    url = rootUri + "onlineshop_add/remove_photo";
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) {
            $('#img1').html("");
            $('#imguri').val('');
        }
    });
}
//]]>
</script>