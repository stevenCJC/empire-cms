<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
DoWapHeader($pagetitle);
?>
<p><b>���D:</b><?=DoWapClearHtml($r[title])?><br/>
<b>�@��:</b><?=DoWapRepF($r[writer],'writer',$ret_r)?><br/>
<b>���:</b><?=date("Y-m-d H:i:s",$r[newstime])?><br/>
<b>���e:</b></p>
<p><?=DoWapRepNewstext($r[newstext])?></p>
<p><br/><a href="<?=$listurl?>">��^�C��</a> <a href="index.php?style=<?=$wapstyle?>">��������</a></p>
<?php
DoWapFooter();
?>