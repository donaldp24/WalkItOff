<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {
		$.validator.messages.required = "必须要填写";
		$.validator.messages.minlength = jQuery.validator.format("密码必须由至少{0}个字符组成.");
		$.validator.messages.maxlength = jQuery.validator.format("密码必须由最多{0}个字符组成");

		$('#validation-form').validate({
			errorElement: 'span',
			errorClass: 'help-inline',
			focusInvalid: false,
			rules: {
				oldpassword : {
					required: true,
					maxlength: 30,
					minlength: 6
				},
				password: {
					required: true,
					maxlength: 30,
					minlength: 6
				},
				confirmpass: {
					required: true,
					minlength: 6,
					maxlength: 30,
					equalTo: "#password"
				},

			},

			messages: {
				confirmpass: {
					equalTo: "密码不一致"
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

				form.submit();
			},
			invalidHandler: function (form) {
			}
		});

	});

/**
 * 암호재설정단추가 눌리우는 경우 hidden 변수에 값설정
 * @param id
 * @returns {boolean}
 */

$(document).ready(function(){
    $("#btn_add").click(function(){
        window.location.href = rootUri + "user_add";
        //$("form").attr("action", rootUri + "user_add");
        //$("form").submit();
        return false;
    })
});

//]]>
</script>