<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
$link=db_connect();
$empire=new mysqlquery();
$editor=1;
//��֤�û�
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
//ehash
$ecms_hashur=hReturnEcmsHashStrAll();
//����
$returnform=RepPostVar($_GET['returnform']);
//��Ŀ¼
$basepath="../../..";
$filepath=RepPostStr($_GET['filepath'],1);
if(strstr($filepath,".."))
{
	$filepath="";
}
$filepath=eReturnCPath($filepath,'');
$openpath=$basepath."/".$filepath;
if(!file_exists($openpath))
{
	$openpath=$basepath;
}
$hand=@opendir($openpath);
db_close();
$empire=null;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>ѡ��Ŀ¼</title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="56%">λ�ã�<a href="ChangePath.php<?=$ecms_hashur['whehref']?>">ѡ��Ŀ¼</a></td>
    <td width="44%"><div align="right"> </div></td>
  </tr>
</table>
<form name="chpath" method="post" action="../enews.php">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <?=$ecms_hashur['eform']?>
    <tr class="header"> 
      <td><div align="center">ѡ��</div></td>
      <td height="25">�ļ��� (��ǰĿ¼��<strong>/ 
        <?=$filepath?>
        </strong>) &nbsp;&nbsp;&nbsp;[<a href="#ecms" onclick="javascript:history.go(-1);">����</a>]</td>
    </tr>
    <?php
	while($file=@readdir($hand))
	{
		if(empty($filepath))
		{
			$truefile=$file;
		}
		else
		{
			$truefile=$filepath."/".$file;
		}
		if($file=="."||$file=="..")
		{
			continue;
		}
		//Ŀ¼
		if(is_dir($openpath."/".$file))
		{
			$filelink="ChangePath.php?filepath=".$truefile."&returnform=".$returnform.$ecms_hashur['ehref'];
			$filename=$file;
			$img="folder.gif";
			$checkbox="";
			$target="";
		}
		//�ļ�
		else
		{
			continue;
		}
	 ?>
    <tr> 
      <td width="12%"><div align="center">
          <input name="path" type="checkbox" id="path" value="../../<?=$truefile?>/" onclick="<?=$returnform?>=this.value;window.close();">
        </div></td>
      <td width="88%" height="25"><img src="../../data/images/dir/<?=$img?>" width="23" height="22"><a href="<?=$filelink?>" title="�鿴�¼�Ŀ¼"> 
        <?=$filename?>
        </a></td>
    </tr>
    <?php
	}
	@closedir($hand);
	?>
  </table>
</form>
</body>
</html>