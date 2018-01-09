<?php
/**
 * 发送邮件
 * @Author   ryan
 * @DateTime 2017-11-28
 * @email    931035553@qq.com
 * @param    str           $to       接收邮箱
 * @param    str           $subject  标题
 * @param    str           $content  文本内容
 * @return   str                  返回结果信息
 */
function sendMail($to, $subject, $content) {
	//导入vender\PHPMailer\classphpmailer.php
	//注意：用vender函数导入的是.php的文件！！！！
	Vendor('PHPMailer.classphpmailer');
	Vendor('PHPMailer.class', '', '.pop3.php');

	$mail = new \PHPMailer(); /*实例化*/
	$mail->IsSMTP(); /*启用SMTP*/
	$mail->Host 		=	C('MAIL_HOST'); /*smtp服务器的名称*/

	$mail->SMTPDebug 	=	C('MAIL_DEBUG'); /*开启调试模式，显示信息*/
	$mail->Port 		=	C('MAIL_PORT'); /*smtp服务器的端口号*/
	$mail->SMTPSecure 	=	C('MAIL_SECURE'); /*注意：要开启PHP中的openssl扩展,smtp服务器的验证方式*/

	$mail->SMTPAuth 	= 	C('MAIL_SMTPAUTH'); /*启用smtp认证*/
	$mail->Username 	= 	C('MAIL_USERNAME'); /*你的邮箱名*/
	$mail->Password 	= 	C('MAIL_PASSWORD') ; /*邮箱密码*/
	$mail->From 		= 	C('MAIL_FROM'); /*发件人地址（也就是你的邮箱地址）*/
	$mail->FromName 	= 	C('MAIL_FROMNAME'); /*发件人姓名*/
	$mail->AddAddress($to,"name");
	$mail->WordWrap 	= 	50; /*设置每行字符长度*/
	$mail->IsHTML(C('MAIL_ISHTML')); /* 是否HTML格式邮件*/
	$mail->CharSet 		=	C('MAIL_CHARSET'); /*设置邮件编码*/
	$mail->Subject 		=	$subject; /*邮件主题*/
	$mail->Body 		= 	$content; /*邮件内容*/
	$mail->AltBody 		= 	"This is the body in plain text for non-HTML mail clients"; /*邮件正文不支持HTML的备用显示*/
	if($mail->Send()) {
		return $content;
	} else {
		return "FALSE: " . $mail->ErrorInfo;
	}
}

/**
 * 遍历数据
 * @Author   ryan
 * @DateTime 2017-11-28
 * @email    931035553@qq.com
 * @param    [type]           $value [description]
 * @param    [type]           $array [description]
 * @return   [type]                  [description]
 */
function deep_in_array($value, $array) {   
    foreach($array as $item) {   
        if(!is_array($item)) {   
            if ($item == $value) {  
                return true;  
            } else {  
                continue;   
            }  
        }   
            
        if(in_array($value, $item)) {  
            return true;      
        } else if(deep_in_array($value, $item)) {  
            return true;      
        }  
    }   
    return false;   
}