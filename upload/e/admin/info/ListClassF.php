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
//��֤Ȩ��
CheckLevel($logininid,$loginin,$classid,"classf");
$url="<a href='../ListClass.php".$ecms_hashur['whehref']."'>������Ŀ</a>&nbsp;>&nbsp;<a href='ListClassF.php".$ecms_hashur['whehref']."'>������Ŀ�ֶ�</a>";
$sql=$empire->query("select * from {$dbtbpre}enewsclassf order by myorder,fid");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>�����ֶ�</title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="50%">λ�ã� 
      <?=$url?>
    </td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit2" value="������Ŀ�ֶ�" onclick="self.location.href='AddClassF.php?enews=AddClassF<?=$ecms_hashur['ehref']?>';">
		&nbsp;&nbsp;
		<input type="button" name="Submit2" value="������Ŀ" onclick="self.location.href='../ListClass.php<?=$ecms_hashur['whehref']?>';">
      </div></td>
  </tr>
</table>
<form name="form1" method="post" action="../ecmsclass.php" onsubmit="return confirm('ȷ��Ҫ����?');">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <?=$ecms_hashur['form']?>
    <tr class="header"> 
      <td width="6%" height="25"><div align="center">˳��</div></td>
      <td width="27%" height="25">
<div align="center">�ֶ���</div></td>
      <td width="27%">
<div align="center">�ֶα�ʶ</div></td>
      <td width="23%"><div align="center">�ֶ�����</div></td>
      <td width="17%" height="25"><div align="center">����</div></td>
    </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  	$ftype=$r[ftype];
  	if($r[flen])
	{
		if($r[ftype]!="TEXT"&&$r[ftype]!="MEDIUMTEXT"&&$r[ftype]!="LONGTEXT")
		{
			$ftype.="(".$r[flen].")";
		}
	}
  ?>
    <tr bgcolor="ffffff"> 
      <td height="25"><div align="center"> 
          <input name="myorder[]" type="text" id="myorder[]" value="<?=$r[myorder]?>" size="3">
          <input type=hidden name=fid[] value=<?=$r[fid]?>>
        </div></td>
      <td height="25"><div align="center"> 
          <?=$r[f]?>
        </div></td>
      <td><div align="center"> 
          <?=$r[fname]?>
        </div></td>
      <td><div align="center">
	  	  <?=$ftype?>
	  </div></td>
      <td height="25"><div align="center"> 
         [<a href='AddClassF.php?enews=EditClassF&fid=<?=$r[fid]?><?=$ecms_hashur['ehref']?>'>�޸�</a>]&nbsp;&nbsp;[<a href='../ecmsclass.php?enews=DelClassF&fid=<?=$r[fid]?><?=$ecms_hashur['href']?>' onclick="return confirm('ȷ��Ҫɾ��?');">ɾ��</a>]
        </div></td>
    </tr>
    <?php
	}
	?>
    <tr bgcolor="ffffff"> 
      <td height="25">&nbsp;</td>
      <td height="25" colspan="4"><input type="submit" name="Submit" value="�޸��ֶ�˳��">
        <font color="#666666">(ֵԽСԽǰ��)</font> 
        <input name="enews" type="hidden" id="enews" value="EditClassFOrder"> 
      </td>
    </tr>
  </table>
</form>
<table width="100%" border="0" cellspacing="1" cellpadding="3" class="tableborder">
  <tr class="header">
    <td height="25">�ֶε���˵��</td>
  </tr>
  <tr>
    <td height="25" bgcolor="#FFFFFF">ʹ�����õ�����Ŀ�Զ����ֶκ�����ReturnClassAddField(��ĿID,�ֶ���)����ĿID=0Ϊ��ǰ��ĿID��ȡ����ֶ����ݿ��ö��Ÿ��������ӣ�<br>
      ȡ��'classtext'�ֶ����ݣ�$value=ReturnClassAddField(0,'classtext'); //$value�����ֶ����ݡ�<br>
      ȡ�ö���ֶ����ݣ�$value=ReturnClassAddField(1,'classid,classtext'); //$value['classtext']�����ֶ����ݡ�</td>
  </tr>
</table>
</body>
</html>
<?php
db_close();
$empire=null;
?>