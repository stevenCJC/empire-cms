<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
require "../".LoadLang("pub/fun.php");
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

//�W�[�Ϥ��H��
function AddPicNews($add,$title,$pic_url,$url,$pic_width,$pic_height,$open_pic,$border,$pictext,$userid,$username){
	global $empire,$dbtbpre;
	if(!$title||!$pic_url||!$url||!$add[classid])
	{printerror("MustEnter","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"picnews");
	$add[classid]=(int)$add[classid];
	$border=(int)$border;
	$sql=$empire->query("insert into {$dbtbpre}enewspic(title,pic_url,url,pic_width,pic_height,open_pic,border,pictext,classid) values('$title','$pic_url','$url','$pic_width','$pic_height','$open_pic',$border,'$pictext',$add[classid]);");
	//�ͦ�js
	$picid=$empire->lastid();
	GetPicJs($picid);
	if($sql)
	{
		//�ާ@��x
		insert_dolog("picid=".$picid."<br>title=".$title);
		printerror("AddPicNewsSuccess","AddPicNews.php?enews=AddPicNews".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�ͦ��Ϥ��H��js
function GetPicJs($picid){
	global $empire,$dbtbpre;
	$r=$empire->fetch1("select * from {$dbtbpre}enewspic where picid='$picid'");
	$string="<a href='".$r[url]."' title='".$r[title]."' target='".$r[open_pic]."'><img src='".$r[pic_url]."' width=".$r[pic_width]." height=".$r[pic_height]." border=".$r[border]."><br>".$r[title]."</a>";
	$string="document.write(\"".addslashes($string)."\");";
	$filename="../../../d/js/pic/pic_".$picid.".js";
	WriteFiletext_n($filename,$string);
}

//�R���Ϥ��H��js
function DelPicJs($picid){
	$filename="../../../d/js/pic/pic_".$picid.".js";
	DelFiletext($filename);
}

//�ק�Ϥ��H��
function EditPicNews($add,$picid,$title,$pic_url,$url,$pic_width,$pic_height,$open_pic,$border,$pictext,$userid,$username){
	global $empire,$dbtbpre;
	$picid=(int)$picid;
	if(!$picid||!$title||!$pic_url||!$url||!$add[classid])
	{printerror("MustEnter","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"picnews");
	$add[classid]=(int)$add[classid];
	$border=(int)$border;
	$sql=$empire->query("update {$dbtbpre}enewspic set title='$title',pic_url='$pic_url',url='$url',pic_width='$pic_width',pic_height='$pic_height',open_pic='$open_pic',border=$border,pictext='$pictext',classid=$add[classid] where picid='$picid'");
	//�ͦ�js
	GetPicJs($picid);
	if($sql)
	{
		//�ާ@��x
		insert_dolog("picid=".$picid."<br>title=".$title);
		printerror("EditPicNewsSuccess","ListPicNews.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�R���Ϥ��H��
function DelPicNews($picid,$userid,$username){
	global $empire,$dbtbpre;
	$picid=(int)$picid;
	if(!$picid)
	{printerror("NotDelPicnewsid","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"picnews");
	$r=$empire->fetch1("select title from {$dbtbpre}enewspic where picid='$picid'");
	$sql=$empire->query("delete from {$dbtbpre}enewspic where picid='$picid'");
	//�R���Ϥ�js
	DelPicJs($picid);
	if($sql)
	{
		//�ާ@��x
		insert_dolog("picid=".$picid."<br>title=".$r[title]);
		printerror("DelPicNewsSuccess","ListPicNews.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//��q�R���Ϥ��H��
function DelPicNews_all($picid,$userid,$username){
	global $empire,$dbtbpre;
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"picnews");
	$count=count($picid);
	if(!$count)
	{printerror("NotDelPicnewsid","history.go(-1)");}
	for($i=0;$i<$count;$i++)
	{
		$picid[$i]=(int)$picid[$i];
		$add.="picid='$picid[$i]' or ";
		//�R���Ϥ�js
		DelPicJs($picid[$i]);
	}
	$add=substr($add,0,strlen($add)-4);
	$sql=$empire->query("delete from {$dbtbpre}enewspic where ".$add);
	if($sql)
	{
		//�ާ@��x
		insert_dolog("");
		printerror("DelPicNewsSuccess","ListPicNews.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
}
//�W�[�Ϥ��s�D
if($enews=="AddPicNews")
{
	$add=$_POST['add'];
	$title=$_POST['title'];
	$pic_url=$_POST['pic_url'];
	$url=$_POST['url'];
	$pic_width=$_POST['pic_width'];
	$pic_height=$_POST['pic_height'];
	$open_pic=$_POST['open_pic'];
	$border=$_POST['border'];
	$pictext=$_POST['pictext'];
	AddPicNews($add,$title,$pic_url,$url,$pic_width,$pic_height,$open_pic,$border,$pictext,$logininid,$loginin);
}
//�ק�Ϥ��s�D
elseif($enews=="EditPicNews")
{
	$add=$_POST['add'];
	$picid=$_POST['picid'];
	$title=$_POST['title'];
	$pic_url=$_POST['pic_url'];
	$url=$_POST['url'];
	$pic_width=$_POST['pic_width'];
	$pic_height=$_POST['pic_height'];
	$open_pic=$_POST['open_pic'];
	$border=$_POST['border'];
	$pictext=$_POST['pictext'];
	EditPicNews($add,$picid,$title,$pic_url,$url,$pic_width,$pic_height,$open_pic,$border,$pictext,$logininid,$loginin);
}
//�R���Ϥ��s�D
elseif($enews=="DelPicNews")
{
	$picid=$_GET['picid'];
	DelPicNews($picid,$logininid,$loginin);
}
//��q�R���Ϥ��s�D
elseif($enews=="DelPicNews_all")
{
	$picid=$_POST['picid'];
	DelPicNews_all($picid,$logininid,$loginin);
}

$start=0;
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$add="";
$search="";
$search.=$ecms_hashur['ehref'];
$classid=(int)$_GET['classid'];
if($classid)
{
	$add=" where classid='$classid'";
    $search.="&classid=$classid";
}
$line=10;//�C�����
$page_line=15;
$offset=$page*$line;
$totalquery="select count(*) as total from {$dbtbpre}enewspic".$add;
$num=$empire->gettotal($totalquery);//���o�`����
$query="select picid,title,pic_url,url,pic_width,pic_height,open_pic,border,pictext from {$dbtbpre}enewspic".$add;
$query.=" order by picid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
//�Ϥ����O
$csql=$empire->query("select classid,classname from {$dbtbpre}enewspicclass order by classid");
while($cr=$empire->fetch($csql))
{
	if($classid==$cr[classid])
	{$select=" selected";}
	else
	{$select="";}
	$class.="<option value=".$cr[classid].$select.">".$cr[classname]."</option>";
}
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
    <td width="50%">��m�G<a href="ListPicNews.php<?=$ecms_hashur['whehref']?>">�޲z�Ϥ��H��</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�W�[�Ϥ��H��" onclick="self.location.href='AddPicNews.php?enews=AddPicNews<?=$ecms_hashur['ehref']?>';">
		&nbsp;&nbsp;
        <input type="button" name="Submit52" value="�޲z�Ϥ��H������" onclick="self.location.href='PicClass.php<?=$ecms_hashur['whehref']?>';">
      </div></td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="1" cellpadding="3">
  <tr>
    <td>�����G
      <select name="classid" id="classid" onchange=window.location='ListPicNews.php?<?=$ecms_hashur['ehref']?>&classid='+this.options[this.selectedIndex].value>
        <option value="0">�Ҧ����O</option>
		<?=$class?>
      </select></td>
  </tr>
</table>
<form name="form1" method="post" action="ListPicNews.php" onsubmit="return confirm('�T�{�n�R��?');">
<input type=hidden name=enews value=DelPicNews_all>
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <?=$ecms_hashur['form']?>
    <tr class="header"> 
      <td width="10%" height="25"><div align="center">ID</div></td>
      <td width="64%" height="25"><div align="center">�w��</div></td>
      <td width="26%" height="25"><div align="center">�ާ@</div></td>
    </tr>
    <?php
	while($r=$empire->fetch($sql))
	{
	?>
    <tr bgcolor="#FFFFFF" id=pic<?=$r[picid]?>> 
      <td height="25"><div align="center">
          <?=$r[picid]?>
        </div></td>
      <td height="25"><div align="center"><a href="<?=$r[url]?>" target="<?=$r[open_pic]?>" title="<?=$r[title]?>"><img src="<?=$r[pic_url]?>" height="<?=$r[pic_height]?>" width="<?=$r[pic_width]?>" border="<?=$r[border]?>"></a><br>
          <?=$r[title]?>
        </div></td>
      <td height="25"><div align="center">[<a href="AddPicNews.php?enews=EditPicNews&picid=<?=$r[picid]?><?=$ecms_hashur['ehref']?>">�ק�</a>] 
          [<a href="ListPicNews.php?enews=DelPicNews&picid=<?=$r[picid]?><?=$ecms_hashur['href']?>" onclick="return confirm('�T�{�n�R��?');">�R��</a> 
          <input name="picid[]" type="checkbox" id="picid[]" value="<?=$r[picid]?>" onclick="if(this.checked){pic<?=$r[picid]?>.style.backgroundColor='#DBEAF5';}else{pic<?=$r[picid]?>.style.backgroundColor='#ffffff';}">
          ]</div></td>
    </tr>
    <?php
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td height="25" colspan="3">&nbsp;
        <?=$returnpage?>
        &nbsp;&nbsp;
        <input type="submit" name="Submit" value="��q�R��"></td>
    </tr>
    <tr bgcolor="#FFFFFF">
      <td height="25" colspan="3"><font color="#666666">JS�եΤ覡�G&lt;script src= 
        <?=$public_r[newsurl]?>
        d/js/pic/pic_�Ϥ��H��ID.js&gt;&lt;/script&gt;</font></td>
    </tr>
  </table>
</form>
</body>
</html>
<?php
db_close();
$empire=null;
?>