<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
$link=db_connect();
$empire=new mysqlquery();
$editor=1;
//���ҥΤ�
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
//ehash
$ecms_hashur=hReturnEcmsHashStrAll();
//�����v��
CheckLevel($logininid,$loginin,$classid,"picnews");
$url="<a href=ListPicNews.php".$ecms_hashur['whehref'].">�޲z�Ϥ��H��</a>&nbsp;>&nbsp;�W�[�Ϥ��H��";
$enews=ehtmlspecialchars($_GET['enews']);
//�ק�Ϥ��H��
if($enews=="EditPicNews")
{
	$picid=(int)$_GET['picid'];
	$r=$empire->fetch1("select title,pic_url,url,pic_width,pic_height,open_pic,border,pictext,classid from {$dbtbpre}enewspic where picid='$picid'");
	$open_pic0=" selected";
	$open_pic1="";
	if($r[open_pic]=="_parent")
	{
		$open_pic0="";
		$open_pic1=" selected";
	}
}
//�Ϥ����O
$sql=$empire->query("select classid,classname from {$dbtbpre}enewspicclass order by classid");
while($cr=$empire->fetch($sql))
{
	if($r[classid]==$cr[classid])
	{$select=" selected";}
	else
	{$select="";}
	$class.="<option value=".$cr[classid].$select.">".$cr[classname]."</option>";
}
db_close();
$empire=null;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>�޲z�Ϥ��H��</title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr>
    <td>��m�G<?=$url?>
    </td>
  </tr>
</table>
<form name="form1" method="post" action="ListPicNews.php">
<?=$ecms_hashur['form']?>
<input type=hidden name=enews value=<?=$enews?>>
<input type=hidden name=picid value=<?=$picid?>>
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
    <tr class="header"> 
      <td height="25" colspan="2">�W�[�Ϥ��H��</td>
    </tr>
    <tr bgcolor="#FFFFFF">
      <td height="25">�п�����O�G</td>
      <td height="25"><select name="add[classid]" id="add[classid]">
          <?=$class?>
        </select>
        <input type="button" name="Submit6222" value="�޲z����" onclick="window.open('PicClass.php<?=$ecms_hashur['whehref']?>');"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="18%" height="25">�Ϥ��a�}�G</td>
      <td width="82%" height="25"><input name="pic_url" type="text" id="pic_url" value="<?=$r[pic_url]?>" size="36">
        <a onclick="window.open('../ecmseditor/FileMain.php?modtype=5&type=1&classid=&doing=2&field=pic_url<?=$ecms_hashur['ehref']?>','','width=700,height=550,scrollbars=yes');" title="��ܤw�W�Ǫ��Ϥ�"><img src="../../data/images/changeimg.gif" width="22" height="22" border="0" align="absbottom"></a> 
        �e 
        <input name="pic_width" type="text" id="pic_width" value="<?=$r[pic_width]?>" size="4">
        �� �� 
        <input name="pic_height" type="text" id="pic_height" value="<?=$r[pic_height]?>" size="4">
        �A��ءG 
        <input name="border" type="text" id="border" value="<?=$r[border]?>" size="2"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="25">�H�����D�G</td>
      <td height="25"><input name="title" type="text" id="title" value="<?=$r[title]?>" size="50"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="25">�챵�a�}�G</td>
      <td height="25"><input name="url" type="text" id="url" value="<?=$r[url]?>" size="50"> 
        <select name=open_pic id="open_pic">
          <option value="_blank"<?=$open_pic0?>>�b�s���f���}</option>
          <option value="_parent"<?=$open_pic1?>>�b�쵡�f���}</option>
        </select></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="25" valign="top">�H��²���G</td>
      <td height="25"><textarea name="pictext" cols="65" rows="6" id="pictext"><?=$r[pictext]?></textarea></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="25">&nbsp;</td>
      <td height="25"><input type="submit" name="Submit" value="����"> <input type="reset" name="Submit2" value="���m"></td>
    </tr>
  </table>
</form>
</body>
</html>