<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?>
<?php
$buycar=getcvar('mybuycar');
if(empty($buycar))
{
	printerror('�A���ʪ����S���ӫ~','',1,0,1);
}
$record="!";
$field="|";
$totalmoney=0;	//�ӫ~�`���B
$buytype=0;	//��I�����G1�����B,0���I��
$totalfen=0;	//�ӫ~�`�n��
$classids='';	//��ض��X
$cdh='';
$buycarr=explode($record,$buycar);
$bcount=count($buycarr);
?>
<table width="100%" border=0 align=center cellpadding=3 cellspacing=1>
<tr class="header"> 
	<td width="41%" height=23><div align="center">�ӫ~�W��</div></td>
	<td width="15%"><div align="center">��������</div></td>
	<td width="15%"><div align="center">�u�f����</div></td>
	<td width="8%"><div align="center">�ƶq</div></td>
	<td width="21%"><div align="center">�p�p</div></td>
</tr>
<?php
for($i=0;$i<$bcount-1;$i++)
{
	$pr=explode($field,$buycarr[$i]);
	$productid=$pr[1];
	$fr=explode(",",$pr[1]);
	//ID
	$classid=(int)$fr[0];
	$id=(int)$fr[1];
	if(empty($class_r[$classid][tbname]))
	{
		continue;
	}
	//�ݩ�
	$addatt='';
	if($pr[2])
	{
		$addatt=$pr[2];
	}
	//�ƶq
	$pnum=(int)$pr[3];
	if($pnum<1)
	{
		$pnum=1;
	}
	//���o���~�H��
	$productr=$empire->fetch1("select title,tprice,price,isurl,titleurl,classid,id,titlepic,buyfen from {$dbtbpre}ecms_".$class_r[$classid][tbname]." where id='$id' limit 1");
	if(!$productr['id']||$productr['classid']!=$classid)
	{
		continue;
	}
	//�O�_�����I��
	if(!$productr[buyfen])
	{
		$buytype=1;
	}
	$thistotalfen=$productr[buyfen]*$pnum;
	$totalfen+=$thistotalfen;
	//���~�Ϥ�
	if(empty($productr[titlepic]))
	{
		$productr[titlepic]="../../data/images/notimg.gif";
	}
	//��^�챵
	$titleurl=sys_ReturnBqTitleLink($productr);
	$thistotal=$productr[price]*$pnum;
	$totalmoney+=$thistotal;
	//��ض��X
	$classids.=$cdh.$productr['classid'];
	$cdh=',';
?>
<tr>
	<td align="center" height=23><a href="<?=$titleurl?>" target="_blank"><?=$productr[title]?></a><?=$addatt?' - '.$addatt:''?></td>
	<td align="right">�D<?=$productr[tprice]?></td>
	<td align="right"><b>�D<?=$productr[price]?></b></td>
	<td align="right"><?=$pnum?></td>
	<td align="right">�D<?=$thistotal?></td>
</tr>
<?php
}
?>
<?php
if(!$buytype)//�I�ƥI�O
{
?>
<tr height="25"> 
    <td colspan="5"><div align="right">�X�p�I��:<strong><?=$totalfen?></strong></div></td>
</tr>
<?php
}
else
{
?>
<tr height="27"> 
    <td colspan="5"><div align="right">�X�p:<strong>�D<?=$totalmoney?></strong></div></td>
</tr>
<?php
}
?>
</table>