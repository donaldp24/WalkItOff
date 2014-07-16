/**
 * 重新载入验证码
 * 对应点击验证码图片的事件
 *
 */
function reload_validate_code(myurl, w, h, s) {

	$.ajax({
		type:	'POST',
		url: rootUri + 'account/reloadCaptcha',
		success:(function(rst) {
			$('#verific_code').html(rst);
		})
	});

}
