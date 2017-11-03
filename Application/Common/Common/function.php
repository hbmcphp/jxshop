<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/15
 * Time: 20:29
 */

function sendemail($to,$Subject,$body)
{
    require '../ThinkPHP/Library/Org/PHPMailer/class.phpmailer.php';
    $mail             = new PHPMailer();
    /*服务器相关信息*/
    $mail->IsSMTP();  //发送方式
    $mail->SMTPAuth   = true;
    $mail->Host       = 'smtp.163.com';   //设置发送的邮件的服务器地址
    $mail->Username   = 'phpresources';  //用户名
    $mail->Password   = 'qazwsxedc123';  //第三方客户端密码
    /*内容信息*/
    $mail->IsHTML(true);
    $mail->CharSet    ="UTF-8";
    $mail->From       = 'phpresources@163.com';	 //发件箱
    $mail->FromName   ="商城管理员";
    $mail->Subject    = $Subject; //主题
    $mail->MsgHTML($body);//具体的正文


    $mail->AddAddress($to);  //指定的用户发送邮件
    // $mail->AddAttachment("test.png"); //追加附件
    $res = $mail->Send();
}

/**
 * 发送模板短信
 * @param to 手机号码集合,用英文逗号分开
 * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
 * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
 */
function sendTemplateSMS($to, $datas, $tempId)
{
    Vendor('sms.CCPRestSmsSDK');
    //主帐号,对应开官网发者主账号下的 ACCOUNT SID
    $accountSid = '8a216da85f008800015f13834f3a076e';

//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
    $accountToken = '0ccbdae1f1474251a2129013bb928b9a';

//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
    $appId = '8a216da85f008800015f139fc5d00808';

//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
    $serverIP = 'sandboxapp.cloopen.com';


//请求端口，生产环境和沙盒环境一致
    $serverPort = '8883';

//REST版本号，在官网文档REST介绍中获得。
    $softVersion = '2013-12-26';

    $rest = new \REST($serverIP, $serverPort, $softVersion);
    $rest->setAccount($accountSid, $accountToken);
    $rest->setAppId($appId);


    $result = $rest->sendTemplateSMS($to, $datas, $tempId);
    if ($result == NULL) {
        return false;
    }
    if ($result->statusCode != 0) {
        return false;
    }
    return true;
}



