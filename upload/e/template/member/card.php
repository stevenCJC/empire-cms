<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?>
<?php
$public_diyr['pagetitle']='�I�d�R��';
$url="<a href=../../../>����</a>&nbsp;>&nbsp;<a href=../cp/>�|������</a>&nbsp;>&nbsp;�I�d�R��";
require(ECMS_PATH.'e/template/incfile/header.php');
?>
<script>
function GetFen1()
{
var ok;
ok=confirm("�T�{�n�R��?");
if(ok)
{
document.GetFen.Submit.disabled=true
return true;
}
else
{return false;}
}
</script>
<br>
<table width="60%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <form name=GetFen method=post action=../doaction.php onsubmit="return GetFen1();">
    <input type=hidden name=enews value=CardGetFen>
    <tr class="header"> 
      <td height="25" colspan="2"><div align="center">�I�d�R��</div></td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td width="34%" height="25"> <div align="right">�R�Ȫ��Τ�W�G</div></td>
      <td width="66%" height="25"> <input name="username" type="text" id="username" value="<?=$user[username]?>">
        *</td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td height="25"> <div align="right">���ƥΤ�W�G</div></td>
      <td height="25"> <input name="reusername" type="text" id="reusername" value="<?=$user[username]?>">
        *</td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td height="25"> <div align="right">�R�ȥd���G</div></td>
      <td height="25"> <input name="card_no" type="text" id="card_no">
        *</td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td height="25"> <div align="right">�R�ȥd�K�X�G</div></td>
      <td height="25"> <input name="password" type="password" id="password">
        *</td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td height="25"> <div align="center"></div></td>
      <td height="25"> <input type="submit" name="Submit" value="�}�l�R��"> &nbsp; 
        <input type="reset" name="Submit2" value="���m"> </td>
    </tr>
    <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"> 
      <td height="25" colspan="2"> <div align="center">�����G�a*�������񶵡C</div></td>
    </tr>
  </form>
</table>
<br>
<?php
require(ECMS_PATH.'e/template/incfile/footer.php');
?>