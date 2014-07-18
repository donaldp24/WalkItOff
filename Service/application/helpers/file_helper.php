<?php
    function DisplayData($recordArray, $fieldStr) {
	    $fieldArray = explode(',', $fieldStr);
	    $result = '';
	    foreach($recordArray as $record) {
		    foreach($fieldArray as $field) {
			    $result .= ($record[trim($field)] ? $record[trim($field)] : '').'|';
		    }
		    $result = substr($result, 0, strlen($result)-1);
		    $result .= '@';
	    }
	    $result = substr($result, 0, strlen($result)-1);
	    return $result;
    }
    function create_link($cur_page,$all_page,$per_page)
    {
        $pagination = "";
        if($cur_page>1)
            $pagination .= "<a href='#' onclick='pagination(".(($cur_page-2)*$per_page).")'>Prev</a>&nbsp;";
        if($all_page<10)
        {
            for($i = 1; $i <= $all_page; $i++)
            {
                if($cur_page == $i)
                    $pagination .= "<strong>".$i."</strong>&nbsp;";
                else
                    $pagination .= "<a href='#' onclick='pagination(".(($i-1)*$per_page).")'>".$i."</a>&nbsp;";
            }
        }else
        {
            for($i = $cur_page-2; $i <= $cur_page+2; $i++)
            {
                if($i>1&&$i<$cur_page)
                {
                    if($cur_page == $i)
                        $pagination .= "<strong>".$i."</strong>&nbsp;";
                    else
                        $pagination .= "<a href='#' onclick='pagination(".(($i-1)*$per_page).")'>".$i."</a>&nbsp;";
                }
            }
        }
        if($cur_page<$all_page)
            $pagination .= "<a href='#' onclick='pagination(".(($cur_page)*$per_page).")'>Next</a>";
        return $pagination;
    }
    function makerandom($count)
    {
        $str = "";
        for($i = 0; $i < $count; $i++)
        {
            $num = rand(0,1);
            if($num == 0)
                $str .= chr(rand(65,90));
            else
                $str .= chr(rand(97,122));
        }
        return $str;
    }
    function push_notification($txt_message, $device_id)
     {

		//이제 아까 만들었던 pem파일을 써먹을 차례다. 경로를 입력하자.
		//만약 작성중인 php 파일과 같은 경로에 있다면
		$apnsCert = './application/controllers/sendinfo/apns_for_dev.pem';
		//그리고 애플의 푸쉬서버와 통신할 stream context를 작성한다.
		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		//그대로 갖다 붙이면 된다;
		//이제 애플의 푸쉬 서버에 연결해보자.
		$apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
		//만약 앱을 배포할 때에는 애플 프로비저닝 포털의 App ID에서 개발용이 아닌 배포용 푸쉬 인증서를 받으신 후
		//키체인에서 다시 인증서와 키를 추출한 후에 pem으로 돌리고 서버에 올리신 후 위에 있는 $apnsCert 변수의
		//파일명을 바꿔주시면 되며, 바로 이 위에 있는 $apns변수의 주소에서 sandbox를 빼주시면 된다.
		//ssl://gateway.push.apple.com
		if (!$apns) {
		    print "Failed to connect $error $errorString\n";
		    return;
		}
		//만약 요청에 실패하면 Failed to connect가 브라우저에 뜰 것이다.
		//자, 이제 드디어 푸쉬를 넣을 차례다!

         // 먼저 푸쉬를 넣을 때 표시할 문구와 기본적인 푸쉬 요소를 입력한다.
         $payload = array();
         $payload['aps'] = array('alert' => $txt_message, 'badge' => 0, 'sound' => 'default');
         //alert은 푸쉬가 도착했을 때 표시할 문구이고 badge는 푸쉬가 도착했을 때 아이콘에 표시할 뱃지 수이고
         //sound는 푸쉬가 도착했을 때 알림 소리이다.
         //이제 이 것을 JSON문법 형태로 고쳐야 한다.
         $push = json_encode($payload);
         //아주 간단하다. 만약 변환된 형태가 궁금하다면 최상단의 링크를 참조하시라.
         //만약 푸쉬를 통해서 앱으로 추가적인 정보를 전달해야 한다면 JSON으로 변환 전 추가적인 작업을 하자.
         //$payload['extra_info'] = array('name' => 'Lifeclue', 'blog' => 'http://blog.naver.com/legendx');
         //이런식으로 하면 푸쉬가 도착했을 때 앱에서 추가적으로 자료를 활용할 수 있다.

		$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_id)) . chr(0) . chr(strlen($push)) . $push;
		$writeResult = fwrite($apns, $apnsMessage);
		//마지막으로 썼던 것은 스스로 정리하자.
		@socket_close($apns);
		fclose($apns);
         return;
    }

    function push_notification_array($array)
    {
        set_time_limit(0);

        //이제 아까 만들었던 pem파일을 써먹을 차례다. 경로를 입력하자.
        //만약 작성중인 php 파일과 같은 경로에 있다면
        $apnsCert = './application/controllers/sendinfo/apns_for_dev.pem';
        //그리고 애플의 푸쉬서버와 통신할 stream context를 작성한다.
        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
        //그대로 갖다 붙이면 된다;
        //이제 애플의 푸쉬 서버에 연결해보자.
        $apns = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);
        //만약 앱을 배포할 때에는 애플 프로비저닝 포털의 App ID에서 개발용이 아닌 배포용 푸쉬 인증서를 받으신 후
        //키체인에서 다시 인증서와 키를 추출한 후에 pem으로 돌리고 서버에 올리신 후 위에 있는 $apnsCert 변수의
        //파일명을 바꿔주시면 되며, 바로 이 위에 있는 $apns변수의 주소에서 sandbox를 빼주시면 된다.
        //ssl://gateway.push.apple.com
        if (!$apns) {
            print "Failed to connect $error $errorString\n";
            return;
        }
        //만약 요청에 실패하면 Failed to connect가 브라우저에 뜰 것이다.
        //자, 이제 드디어 푸쉬를 넣을 차례다!
        foreach ($array as $item) {
            // 먼저 푸쉬를 넣을 때 표시할 문구와 기본적인 푸쉬 요소를 입력한다.
            $payload = array();
            $payload['aps'] = array('alert' => $item['msg'], 'badge' => 0, 'sound' => 'default');
            //alert은 푸쉬가 도착했을 때 표시할 문구이고 badge는 푸쉬가 도착했을 때 아이콘에 표시할 뱃지 수이고
            //sound는 푸쉬가 도착했을 때 알림 소리이다.
            //이제 이 것을 JSON문법 형태로 고쳐야 한다.
            $push = json_encode($payload);
            //아주 간단하다. 만약 변환된 형태가 궁금하다면 최상단의 링크를 참조하시라.
            //만약 푸쉬를 통해서 앱으로 추가적인 정보를 전달해야 한다면 JSON으로 변환 전 추가적인 작업을 하자.
            //$payload['extra_info'] = array('name' => 'Lifeclue', 'blog' => 'http://blog.naver.com/legendx');
            //이런식으로 하면 푸쉬가 도착했을 때 앱에서 추가적으로 자료를 활용할 수 있다.
            $device_id = $item['deviceId'];
            log_message('info', 'push notification -device id = ' . $device_id);
            $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_id)) . chr(0) . chr(strlen($push)) . $push;
            $writeResult = fwrite($apns, $apnsMessage);
        }

        //마지막으로 썼던 것은 스스로 정리하자.
        @socket_close($apns);
        fclose($apns);
        return;
    }
?>