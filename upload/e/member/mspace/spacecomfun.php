<?php
//--------------- �|���Ŷ���� ---------------

//��ܪŶ��ҪO
function ChangeSpaceStyle($add){
	global $empire,$dbtbpre;
	$user_r=islogin();//�O�_�n��
	$styleid=intval($add['styleid']);
	if(!$styleid)
	{
		printerror('NotChangeSpaceStyleId','',1);
	}
	$sr=$empire->fetch1("select styleid,membergroup from {$dbtbpre}enewsspacestyle where styleid='$styleid'");
	if(!$sr['styleid']||($sr['membergroup']&&!strstr($sr['membergroup'],','.$user_r[groupid].',')))
	{
		printerror('NotChangeSpaceStyleId','',1);
	}
	$sql=$empire->query("update {$dbtbpre}enewsmemberadd set spacestyleid='$styleid' where userid='$user_r[userid]' limit 1");
	if($sql)
	{
		printerror('ChangeSpaceStyleSuccess','ChangeStyle.php',1);
	}
	else
	{
		printerror('DbError','',1);
	}
}

//�]�m�Ŷ�
function DoSetSpace($add){
	global $empire,$dbtbpre;
	$user_r=islogin();//�O�_�n��
	$spacename=RepPostStr($add['spacename']);
	$spacegg=RepPostStr($add['spacegg']);
	$sql=$empire->query("update {$dbtbpre}enewsmemberadd set spacename='$spacename',spacegg='$spacegg' where userid='$user_r[userid]' limit 1");
	if($sql)
	{
		printerror('SetSpaceSuccess','SetSpace.php',1);
	}
	else
	{
		printerror('DbError','',1);
	}
}
?>