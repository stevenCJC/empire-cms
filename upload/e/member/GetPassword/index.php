<?php
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/q_functions.php");
require("../class/user.php");
$link=db_connect();
$empire=new mysqlquery();
$editor=1;
eCheckCloseMods('member');//�����Ҷ�
if(!$public_r['opengetpass'])
{
	printerror('CloseGetPassword','',1);
}
//�ɤJ�ҪO
require(ECMS_PATH.'e/template/member/GetPassword.php');
db_close();
$empire=null;
?>