<?php
if(!defined('InEmpireCMS'))
{
	exit();
}

//�έp�X��
function UpdateSpaceViewStats($userid){
	global $empire,$dbtbpre;
	if(!getcvar('dospacevstats'.$userid))
	{
		$sql=$empire->query("update {$dbtbpre}enewsmemberadd set viewstats=viewstats+1 where userid='".$userid."' limit 1");
		esetcookie("dospacevstats".$userid,1,time()+3600);
	}
}

//����
if($public_r['openspace']==1)
{
	printerror('CloseMemberSpace','',1);
}

require_once ECMS_PATH.'e/space/spacefun.php';

//�Τ�O�_�s�b
$userid=intval($_GET['userid']);
if($userid)
{
	$add="userid=$userid";
	$username='';
	$utfusername='';
	$uadd=egetmf('userid')."='$userid'";
}
else
{
	$username=RepPostVar($_GET['username']);
	if(empty($username))
	{
		printerror("NotUsername","",1);
	}
	$add="username='$username'";
	$utfusername=$username;
	$uadd=egetmf('username')."='$username'";
}
$ur=$empire->fetch1("select ".eReturnSelectMemberF('*')." from ".eReturnMemberTable()." where ".$uadd." limit 1");
if(empty($ur['username']))
{
	printerror("NotUsername","",1);
}
$userid=$userid?$userid:$ur['userid'];
$utfusername=$utfusername?$utfusername:$ur['username'];
$username=$username?$username:$ur['username'];
$groupid=$ur['groupid'];
UpdateSpaceViewStats($userid);//�έp�X��
$addur=$empire->fetch1("select * from {$dbtbpre}enewsmemberadd where userid='".$userid."' limit 1");
//�Y��
$userpic=$addur['userpic']?$addur['userpic']:$public_r[newsurl].'e/data/images/nouserpic.gif';
//�Ŷ��a�}
$spaceurl=eReturnDomainSiteUrl()."e/space/?userid=".$userid;
//�Ŷ��W��
$spacename=$addur['spacename']?$addur['spacename']:$username." ���Ŷ�";
//�Ŷ��ҪO
$spacestyleid=$addur['spacestyleid'];
if(empty($spacestyleid))
{
	$spacestyleid=$public_r['defspacestyleid'];
}
$spacestyler=$empire->fetch1("select stylepath from {$dbtbpre}enewsspacestyle where styleid='$spacestyleid'");
$spacestyle=$spacestyler['stylepath']?$spacestyler['stylepath']:'default';
?>