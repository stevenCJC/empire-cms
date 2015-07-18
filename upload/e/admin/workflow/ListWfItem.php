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
CheckLevel($logininid,$loginin,$classid,"workflow");

//��^�Τ��
function ReturnWfGroup($groupid){
	$count=count($groupid);
	if($count==0)
	{
		return '';
	}
	$ids=',';
	for($i=0;$i<$count;$i++)
	{
		$ids.=$groupid[$i].',';
	}
	return $ids;
}

//�W�[�`�I
function AddWorkflowItem($add,$userid,$username){
	global $empire,$dbtbpre;
	$wfid=(int)$add['wfid'];
	$tno=(int)$add['tno'];
	$lztype=(int)$add['lztype'];
	$tbdo=(int)$add['tbdo'];
	$tddo=(int)$add['tddo'];
	if(!$wfid||!$tno)
	{
		printerror('EmptyWorkflowItem','history.go(-1)');
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"workflow");
	$num=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsworkflowitem where wfid='$wfid' and tno='$tno' limit 1");
	if($num)
	{
		printerror('HaveWorkflowItem','history.go(-1)');
	}
	$groupid=ReturnWfGroup($add[groupid]);
	$userclass=ReturnWfGroup($add[userclass]);
	$username=','.$add[username].',';
	if($groupid==''&&$userclass==''&&$add[username]=='')
	{
		printerror('EmptyWorkflowItemUser','history.go(-1)');
	}
	$sql=$empire->query("insert into {$dbtbpre}enewsworkflowitem(wfid,tname,tno,ttext,groupid,userclass,username,lztype,tbdo,tddo,tstatus) values('$wfid','$add[tname]','$tno','$add[ttext]','$groupid','$userclass','$username','$lztype','$tbdo','$tddo','$add[tstatus]');");
	$tid=$empire->lastid();
	if($sql)
	{
		//�ާ@��x
		insert_dolog("wfid=$wfid&tid=$tid<br>tname=".$add[tname]);
		printerror("AddWorkflowItemSuccess","AddWfItem.php?enews=AddWorkflowItem&wfid=$wfid".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�ק�`�I
function EditWorkflowItem($add,$userid,$username){
	global $empire,$dbtbpre;
	$tid=(int)$add['tid'];
	$wfid=(int)$add['wfid'];
	$tno=(int)$add['tno'];
	$lztype=(int)$add['lztype'];
	$tbdo=(int)$add['tbdo'];
	$tddo=(int)$add['tddo'];
	if(!$tid||!$wfid||!$tno)
	{
		printerror('EmptyWorkflowItem','history.go(-1)');
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"workflow");
	$num=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsworkflowitem where wfid='$wfid' and tno='$tno' and tid<>$tid limit 1");
	if($num)
	{
		printerror('HaveWorkflowItem','history.go(-1)');
	}
	$groupid=ReturnWfGroup($add[groupid]);
	$userclass=ReturnWfGroup($add[userclass]);
	$username=','.$add[username].',';
	if($groupid==''&&$userclass==''&&$add[username]=='')
	{
		printerror('EmptyWorkflowItemUser','history.go(-1)');
	}
	$sql=$empire->query("update {$dbtbpre}enewsworkflowitem set tname='$add[tname]',tno='$tno',ttext='$add[ttext]',groupid='$groupid',userclass='$userclass',username='$username',lztype='$lztype',tbdo='$tbdo',tddo='$tddo',tstatus='$add[tstatus]' where tid='$tid'");
	if($sql)
	{
		//�ާ@��x
		insert_dolog("wfid=$wfid&tid=$tid<br>tname=".$add[tname]);
		printerror("EditWorkflowItemSuccess","ListWfItem.php?wfid=$wfid".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�R���`�I
function DelWorkflowItem($add,$userid,$username){
	global $empire,$dbtbpre;
	$tid=(int)$add[tid];
	$wfid=(int)$add['wfid'];
	if(!$tid||!$wfid)
	{
		printerror("NotDelWorkflowItemid","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"workflow");
	$r=$empire->fetch1("select tname from {$dbtbpre}enewsworkflowitem where tid='$tid'");
	$sql=$empire->query("delete from {$dbtbpre}enewsworkflowitem where tid='$tid'");
	if($sql)
	{
		//�ާ@��x
		insert_dolog("wfid=$wfid&tid=$tid<br>tname=".$r[tname]);
		printerror("DelWorkflowItemSuccess","ListWfItem.php?wfid=$wfid".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�ק�`�I�s��
function EditWorkflowItemTno($add,$userid,$username){
	global $empire,$dbtbpre;
	$wfid=(int)$add['wfid'];
	$tno=$add[tno];
	$tid=$add[tid];
	for($i=0;$i<count($tid);$i++)
	{
		$newtno=(int)$tno[$i];
		if(empty($newtno))
		{
			continue;
		}
		$newtid=(int)$tid[$i];
		$empire->query("update {$dbtbpre}enewsworkflowitem set tno='$newtno' where tid='$newtid'");
    }
	//�ާ@��x
	insert_dolog("wfid=$wfid");
	printerror("EditWorkflowItemSuccess","ListWfItem.php?wfid=$wfid".hReturnEcmsHashStrHref2(0));
}

$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
}
if($enews=="AddWorkflowItem")//�W�[�`�I
{
	AddWorkflowItem($_POST,$logininid,$loginin);
}
elseif($enews=="EditWorkflowItem")//�ק�`�I
{
	EditWorkflowItem($_POST,$logininid,$loginin);
}
elseif($enews=="DelWorkflowItem")//�R���`�I
{
	DelWorkflowItem($_GET,$logininid,$loginin);
}
elseif($enews=="EditWorkflowItemTno")//�ק�`�I�s��
{
	EditWorkflowItemTno($_POST,$logininid,$loginin);
}

$wfid=(int)$_GET['wfid'];
if(!$wfid)
{
	printerror('ErrorUrl','');
}
$wfr=$empire->fetch1("select wfid,wfname from {$dbtbpre}enewsworkflow where wfid='$wfid'");
if(!$wfr['wfid'])
{
	printerror('ErrorUrl','');
}
$query="select tid,tname,tno,lztype from {$dbtbpre}enewsworkflowitem where wfid='$wfid' order by tno,tid";
$sql=$empire->query($query);
$url="<a href=ListWf.php".$ecms_hashur['whehref'].">�޲z�u�@�y</a> &gt; ".$wfr[wfname]." &gt; <a href='ListWfItem.php?wfid=$wfid".$ecms_hashur['ehref']."'>�޲z�`�I</a>";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>�u�@�y</title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" cellspacing="1" cellpadding="3">
  <tr> 
    <td width="50%">��m: 
      <?=$url?>
    </td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�W�[�`�I" onclick="self.location.href='AddWfItem.php?enews=AddWorkflowItem&wfid=<?=$wfid?><?=$ecms_hashur['ehref']?>';">
      </div></td>
  </tr>
</table>
<br>
  
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <form name="form1" method="post" action="ListWfItem.php">
  <?=$ecms_hashur['form']?>
    <tr class="header"> 
      <td width="10%"><div align="center">�s��</div></td>
      <td width="44%" height="25"> <div align="center">�`�I�W��</div></td>
      <td width="16%"><div align="center">�y��覡</div></td>
      <td width="23%" height="25"><div align="center">�ާ@</div></td>
    </tr>
    <?php
  while($r=$empire->fetch($sql))
  {
  ?>
    <tr bgcolor="#FFFFFF" onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#C3EFFF'"> 
      <td><div align="center"> 
          <input name="tno[]" type="text" id="tno[]" value="<?=$r[tno]?>" size="5">
		<input type="hidden" name="tid[]" value="<?=$r[tid]?>">
        </div></td>
      <td height="25"> 
        <?=$r[tname]?>
      </td>
      <td><div align="center"> 
          <?=$r[lztype]==1?'�|ñ':'���q�y��'?>
        </div></td>
      <td height="25"><div align="center">[<a href="AddWfItem.php?enews=EditWorkflowItem&tid=<?=$r[tid]?>&wfid=<?=$wfid?><?=$ecms_hashur['ehref']?>">�ק�</a>] 
          [<a href="AddWfItem.php?enews=AddWorkflowItem&tid=<?=$r[tid]?>&wfid=<?=$wfid?>&docopy=1<?=$ecms_hashur['ehref']?>">�ƻs</a>] 
          [<a href="ListWfItem.php?enews=DelWorkflowItem&tid=<?=$r[tid]?>&wfid=<?=$wfid?><?=$ecms_hashur['href']?>" onclick="return confirm('�T�{�n�R��?');">�R��</a>]</div></td>
    </tr>
    <?php
  }
  ?>
    <tr bgcolor="#FFFFFF"> 
      <td height="25" colspan="4"> <input type="submit" name="Submit" value="�ק�s��"> 
        <input name="enews" type="hidden" id="enews" value="EditWorkflowItemTno">
        <input name="wfid" type="hidden" id="wfid" value="<?=$wfid?>"> </td>
    </tr>
  </form>
</table>
</body>
</html>
<?php
db_close();
$empire=null;
?>