<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
//�S������
if($GLOBALS['ecmsadderrorurl'])//�W�[�H��
{
	$error='<br>'.$error.'<br><br><a href="'.$GLOBALS['ecmsadderrorurl'].'">��^�H���C��</a>';
}

//����
$loginadminstyleid=EcmsReturnAdminStyle();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>�H������</title>
<link href="<?=$a?>adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
<?php
if(!$noautourl)
{
?>
<SCRIPT language=javascript>
var secs=2;//3��
for(i=1;i<=secs;i++) 
{ window.setTimeout("update(" + i + ")", i * 1000);} 
function update(num) 
{ 
if(num == secs) 
{ <?=$gotourl_js?>; } 
else 
{ } 
}
</SCRIPT>
<?php
}
?>
</head>

<body>
<br>
<br>
<br>
<br>
<br>
<br>
<table width="500" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td height="25"><div align="center">�H������</div></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="80"> 
      <div align="center">
	  <br>
        <b><?=$error?></b>
        <br>
        <br><a href="<?=$gotourl?>">�p�G�z���s�����S���۰ʸ���A���I���o��</a>
<br><br>
	  </div></td>
  </tr>
</table>
</body>
</html>