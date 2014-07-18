<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "notice";
		return false;
    });
    $("#save_btn").click(function(){
        var check_val = 0;
        $(':checkbox:checked').each(function(){
            var i = 0;
            if($(this).attr('id') == 'level1members')
                i = (1 << 1);
            else if ($(this).attr('id') == 'level2members')
                i = (1 << 2);
            else if ($(this).attr('id') == 'level3members')
                i = (1 << 3);
            else if ($(this).attr('id') == 'level4members')
                i = (1 << 4);
            else if ($(this).attr('id') == 'level5members')
                i = (1 << 5);
            check_val = (check_val + i);
        });
        $('#receiver').attr('value', check_val);
        return true;
    });

    $('#allmembers').on('click' , function(){
        var that = this;
        $(this).parent().find('input:checkbox')
            .each(function(){
                this.checked = that.checked;
                //$(this).closest('tr').toggleClass('selected');
            });

    });

    $.validator.messages.required = "必须要填写";
	$.validator.messages.maxlength = jQuery.validator.format("密码必须由最多{0}个字符组成");

    $('#validation-form').validate({
        errorElement: 'span',
        errorClass: 'help-inline',
        focusInvalid: false,
        rules: {
            title: {
				required:true,
				maxlength: 50
			},
			contents: {
				required:true,
				maxlength: 50
			}
        },

        messages: {

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