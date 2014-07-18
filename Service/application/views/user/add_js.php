<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "user";
		return false;
    })
	

	$.validator.messages.required = "必须要填写";

	$('#validation-form').validate({
		errorElement: 'span',
		errorClass: 'help-inline',
		focusInvalid: false,
		rules: {
			userid: {
				required: true,
				memberid: 'required'
			},
			username: {
				required: true,
				username: 'required'
			},
			phonenum: {
				required: true,
                phonenum: 'required'
			},
            mailaddr : {
                required: true,
                email: true
            },
            job : 'required',
            password: {
                required: true, minlength: 5
            },
            confirmpassword: {
                required: true, equalTo: "#password", minlength: 5
            }
        },

		messages: {
            mailaddr: {
                email: "请输入正确的邮箱地址"
            },
			password: {
				minlength: "请输入至少5个数字"
			},
            confirmpassword: {
                minlength:"请输入至少5个数字",
                equalTo:"请输入相同的密码"
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

function isValidDecimal(value)
{
//	var regexTest = /^\d{0,8}(\.\d{0,2})?$/;
	var regexTest = /^\d+(?:\.\d\d?)?$/;
	var ok = regexTest.test(value);
	return ok;
}

//]]>
</script>