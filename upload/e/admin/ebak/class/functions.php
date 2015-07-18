<?php
$editor=1;
//�r�ŹL�{
function escape_str($str){
	$str=mysql_real_escape_string($str);
	$str=str_replace('\\\'','\'\'',$str);
	$str=str_replace("\\\\","\\\\\\\\",$str);
	$str=str_replace('$','\$',$str);
	return $str;
}

//�״_��
function Ebak_Rep($tablename,$dbname,$userid,$username){
	global $empire,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	$empire->usequery("use `$dbname`");
	$count=count($tablename);
	if(empty($count))
	{printerror("MustChangeOneTable","history.go(-1)");}
	for($i=0;$i<$count;$i++)
	{
		//$sql1=$empire->query("OPTIMIZE TABLE `$tablename[$i]`;");
		//$sql2=$empire->query("CHECK TABLE `$tablename[$i]`;");
		//$sql3=$empire->query("ANALYZE TABLE `$tablename[$i]`;");
		$sql4=$empire->query("REPAIR TABLE `$tablename[$i]`;");
    }
	$empire->usequery("use `".$ecms_config['db']['dbname']."`");
	//�ާ@��x
	insert_dolog("dbname=".$dbname);
	printerror("RepireTableSuccess","ChangeTable.php?mydbname=$dbname".hReturnEcmsHashStrHref2(0));
}

//�u�ƪ�
function Ebak_Opi($tablename,$dbname,$userid,$username){
	global $empire,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	$empire->usequery("use `$dbname`");
	$count=count($tablename);
	if(empty($count))
	{printerror("MustChangeOneTable","history.go(-1)");}
	for($i=0;$i<$count;$i++)
	{
		$sql1=$empire->query("OPTIMIZE TABLE `$tablename[$i]`;");
    }
	$empire->usequery("use `".$ecms_config['db']['dbname']."`");
	//�ާ@��x
	insert_dolog("dbname=".$dbname);
	printerror("OptimTableSuccess","ChangeTable.php?mydbname=$dbname".hReturnEcmsHashStrHref2(0));
}

//�R���ƾڪ�
function Ebak_Drop($tablename,$dbname,$userid,$username){
	global $empire,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	$empire->usequery("use `$dbname`");
	$count=count($tablename);
	if(empty($count))
	{printerror("MustChangeOneDelTable","history.go(-1)");}
	$a="";
	$first=1;
	for($i=0;$i<$count;$i++)
	{
		if(empty($first))
		{
			$a.=",";
	    }
		else
		{
			$first=0;
		}
		$a.="`".$tablename[$i]."`";
    }
	$sql1=$empire->query("DROP TABLE IF EXISTS ".$a.";");
	$empire->usequery("use `".$ecms_config['db']['dbname']."`");
	//�ާ@��x
	insert_dolog("dbname=".$dbname);
	printerror("DelTableSuccess","ChangeTable.php?mydbname=$dbname".hReturnEcmsHashStrHref2(0));
}

//�R���ƾڮw
function Ebak_DropDb($dbname,$userid,$username){
	global $empire;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	if(empty($dbname))
	{printerror("ChangeDropDb","history.go(-1)");}
	$sql=$empire->query("DROP DATABASE `$dbname`");
	if($sql)
	{
		//�ާ@��x
		insert_dolog("dbname=".$dbname);
		printerror("DropDbSuccess","ChangeDb.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�إ߼ƾڮw
function Ebak_CreatDb($dbname,$dbchar,$userid,$username){
	global $empire,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	if(!trim($dbname)){
		printerror("EmptyDbname","history.go(-1)");
	}
	$a="";
	if($dbchar&&$ecms_config['db']['dbver']>='4.1'){
		$a=" DEFAULT CHARACTER SET ".$dbchar;
	}
	$sql=$empire->query("CREATE DATABASE IF NOT EXISTS `$dbname`".$a);
	if($sql){
		//�ާ@��x
		insert_dolog("dbname=".$dbname);
		printerror("AddDbSuccess","ChangeDb.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�M�Ū�
function Ebak_EmptyTable($tablename,$dbname,$userid,$username){
	global $empire,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($dbname);
	$empire->usequery("use `$dbname`");
	$count=count($tablename);
	if(empty($count))
	{printerror("MustChangeOneTable","history.go(-1)");}
	for($i=0;$i<$count;$i++)
	{
		$sql1=$empire->query("TRUNCATE `".$tablename[$i]."`;");
    }
	$empire->usequery("use `".$ecms_config['db']['dbname']."`");
	//�ާ@��x
	insert_dolog("dbname=".$dbname);
	printerror("EmptyTableSuccess","ChangeTable.php?mydbname=$dbname".hReturnEcmsHashStrHref2(0));
}

//---------------------------�ƥ�
//��ϤƳƥ�
function Ebak_DoEbak($add,$userid,$username){
	global $empire,$public_r,$fun_r,$ecms_config;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$dbname=RepPostVar($add['mydbname']);
	if(empty($dbname)){
		printerror("NotChangeDbname","history.go(-1)");
	}
	$tablename=$add['tablename'];
	$count=count($tablename);
	if(empty($count)){
		printerror("MustChangeOneTable","history.go(-1)");
	}
	$add['baktype']=(int)$add['baktype'];
	$add['filesize']=(int)$add['filesize'];
	$add['bakline']=(int)$add['bakline'];
	$add['autoauf']=(int)$add['autoauf'];
	if((!$add['filesize']&&!$add['baktype'])||(!$add['bakline']&&$add['baktype'])){
		printerror("FileSizeEmpty","history.go(-1)");
	}
	//�ؿ��W
	$add['mypath']=RepPostStr($add['mypath'],1);
	$bakpath=$public_r['bakdbpath'];
	if(empty($add['mypath'])){
		$add['mypath']=$dbname."_".date("YmdHis");
	}
    DoMkdir($bakpath."/".$add['mypath']);
	//�ͦ��������
	$readme=$add['readme'];
	$rfile=$bakpath."/".$add['mypath']."/readme.txt";
	$readme.="\r\n\r\nBaktime: ".date("Y-m-d H:i:s");
	WriteFiletext_n($rfile,$readme);

	$b_table="";
	$d_table="";
	for($i=0;$i<$count;$i++){
		$tablename[$i]=RepPostVar($tablename[$i]);
		$b_table.=$tablename[$i].",";
		$d_table.="\$tb[".$tablename[$i]."]=0;\r\n";
    }
	//�h���̫�@��,
	$b_table=substr($b_table,0,strlen($b_table)-1);
	$bakstru=(int)$add['bakstru'];
	$bakstrufour=(int)$add['bakstrufour'];
	$beover=(int)$add['beover'];
	$waitbaktime=(int)$add['waitbaktime'];
	$bakdatatype=(int)$add['bakdatatype'];
	if($add['insertf']=='insert'){
		$insertf='insert';
	}
	else{
		$insertf='replace';
	}
	if($ecms_config['db']['dbver']=='4.0'&&$add['dbchar']=='auto')
	{
		$add['dbchar']='';
	}
	$string="<?php
	\$b_table=\"".$b_table."\";
	".$d_table."
	\$b_baktype=".$add['baktype'].";
	\$b_filesize=".$add['filesize'].";
	\$b_bakline=".$add['bakline'].";
	\$b_autoauf=".$add['autoauf'].";
	\$b_dbname=\"".$dbname."\";
	\$b_stru=".$bakstru.";
	\$b_strufour=".$bakstrufour.";
	\$b_dbchar=\"".addslashes($add['dbchar'])."\";
	\$b_beover=".$beover.";
	\$b_insertf=\"".addslashes($insertf)."\";
	\$b_autofield=\",".addslashes($add['autofield']).",\";
	\$b_bakdatatype=".$bakdatatype.";
	?>";
	$cfile=$bakpath."/".$add['mypath']."/config.php";
	WriteFiletext_n($cfile,$string);
	if($add['baktype']){
		$phome='BakExeT';
	}
	else{
		$phome='BakExe';
	}
	echo $fun_r['FirstBakSuccess']."<script>self.location.href='phome.php?phome=$phome&t=0&s=0&p=0&mypath=$add[mypath]&waitbaktime=$waitbaktime".hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//����ƥ�(�����j�p)
function Ebak_BakExe($t,$s,$p,$mypath,$alltotal,$thenof,$fnum,$stime=0,$userid,$username){
	global $empire,$public_r,$fun_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	if(empty($mypath)){
		printerror("ErrorUrl","history.go(-1)");
	}
	$bakpath=$public_r['bakdbpath'];
	$path=$bakpath."/".$mypath;
	@include($path."/config.php");
	if(empty($b_table)){
		printerror("ErrorUrl","history.go(-1)");
	}
	$waitbaktime=(int)$_GET['waitbaktime'];
	if(empty($stime))
	{
		$stime=time();
	}
	$header="<?php
@include(\"../../inc/header.php\");
";
	$footer="
@include(\"../../inc/footer.php\");
?>";
	$btb=explode(",",$b_table);
	$count=count($btb);
	$t=(int)$t;
	$s=(int)$s;
	$p=(int)$p;
	//�ƥ�����
	if($t>=$count)
	{
		insert_dolog("dbname=".$b_dbname);//�ާ@��x
		$varmessage=$fun_r['BakSuccess']."<br><br>".$fun_r['TotalUseTime'].ToChangeUseTime($stime);
		$GLOBALS['varmessage']=$varmessage;
		printerror('VarMessage','ChangeDb.php'.hReturnEcmsHashStrHref2(1),0,1);
    }
	$dumpsql=Ebak_ReturnVer();
	//��ܼƾڮw
	$u=$empire->usequery("use `$b_dbname`");
	//�s�X
	if($b_dbchar=='auto')
	{
		if(empty($s))
		{
			$status_r=Ebak_GetTotal($b_dbname,$btb[$t]);
			$collation=Ebak_GetSetChar($status_r['Collation']);
			DoSetDbChar($collation);
			//�`�O����
			$num=$public_r[limittype]?-1:$status_r['Rows'];
		}
		else
		{
			$collation=$_GET['collation'];
			DoSetDbChar($collation);
			$num=(int)$alltotal;
		}
		$dumpsql.=Ebak_ReturnSetNames($collation);
	}
	else
	{
		DoSetDbChar($b_dbchar);
		if(empty($s))
		{
			//�`�O����
			if($public_r[limittype])
			{
				$num=-1;
			}
			else
			{
				$status_r=Ebak_GetTotal($b_dbname,$btb[$t]);
				$num=$status_r['Rows'];
			}
		}
		else
		{
			$num=(int)$alltotal;
		}
	}
	//�ƥ��ƾڮw���c
	if($b_stru&&empty($s))
	{
		$dumpsql.=Ebak_Returnstru($btb[$t],$b_strufour);
	}
	$sql=$empire->query("select * from `".$btb[$t]."` limit $s,$num");
	//���o�r�q��
	if(empty($fnum))
	{
		$return_fr=Ebak_ReturnTbfield($b_dbname,$btb[$t],$b_autofield);
		$fieldnum=$return_fr['num'];
		$noautof=$return_fr['autof'];
	}
	else
	{
		$fieldnum=$fnum;
		$noautof=$thenof;
	}
	//���㴡�J
	$inf='';
	if($b_beover==1)
	{
		$inf='('.Ebak_ReturnInTbfield($b_dbname,$btb[$t]).')';
	}
	//�Q���i��
	$hexf='';
	if($b_bakdatatype==1)
	{
		$hexf=Ebak_ReturnInStrTbfield($b_dbname,$btb[$t]);
	}
	$b=0;
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$s++;
		$dumpsql.="E_D(\"".$b_insertf." into `".$btb[$t]."`".$inf." values(";
		$first=1;
		for($i=0;$i<$fieldnum;$i++)
		{
			//���r�q
			if(empty($first))
			{
				$dumpsql.=',';
			}
			else
			{
				$first=0;
			}
			$myi=$i+1;
			if(!isset($r[$i])||strstr($noautof,','.$myi.','))
			{
				$dumpsql.='NULL';
			}
			else
			{
				$dumpsql.=Ebak_ReSqlFtext($r[$i],$b_bakdatatype,$myi,$hexf);
			}
		}
		$dumpsql.=");\");\r\n";
		//�O�_�W�L����
		if(strlen($dumpsql)>=$b_filesize*1024)
		{
			$p++;
			$sfile=$path."/".$btb[$t]."_".$p.".php";
			$dumpsql=$header.$dumpsql.$footer;
			WriteFiletext_n($sfile,$dumpsql);
			$empire->free($sql);
			//echo $fun_r['BakOneDataSuccess'].Ebak_EchoBakSt($btb[$t],$count,$t,$num,$s)."<script>self.location.href='phome.php?phome=BakExe&s=$s&p=$p&t=$t&mypath=$mypath&alltotal=$num&thenof=$noautof&fieldnum=$fieldnum&stime=$stime".hReturnEcmsHashStrHref(0)."';</script>";

			echo"<meta http-equiv=\"refresh\" content=\"".$waitbaktime.";url=phome.php?phome=BakExe&s=$s&p=$p&t=$t&mypath=$mypath&alltotal=$num&thenof=$noautof&fieldnum=$fieldnum&stime=$stime&waitbaktime=$waitbaktime&collation=$collation".hReturnEcmsHashStrHref(0)."\">".$fun_r['BakOneDataSuccess'].Ebak_EchoBakSt($btb[$t],$count,$t,$num,$s);
			exit();
		}
	}
	//�̫�@�ӳƥ�
	if(empty($p)||$b==1)
	{
		$p++;
		$sfile=$path."/".$btb[$t]."_".$p.".php";
		$dumpsql=$header.$dumpsql.$footer;
		WriteFiletext_n($sfile,$dumpsql);
	}
	Ebak_RepFilenum($p,$btb[$t],$path);
	$t++;
	$empire->free($sql);
	//�i�J�U�@�Ӫ�
	//echo $btb[$t-1].$fun_r['OneTableBakSuccess']."<script>self.location.href='phome.php?phome=BakExe&s=0&p=0&t=$t&mypath=$mypath&stime=$stime".hReturnEcmsHashStrHref(0)."';</script>";

	echo"<meta http-equiv=\"refresh\" content=\"".$waitbaktime.";url=phome.php?phome=BakExe&s=0&p=0&t=$t&mypath=$mypath&stime=$stime&waitbaktime=$waitbaktime".hReturnEcmsHashStrHref(0)."\">".$btb[$t-1].$fun_r['OneTableBakSuccess'];
	exit();
}

//����ƥ��]���O���^
function Ebak_BakExeT($t,$s,$p,$mypath,$alltotal,$thenof,$fnum,$auf='',$aufval=0,$stime=0,$userid,$username){
	global $empire,$public_r,$fun_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	if(empty($mypath)){
		printerror("ErrorUrl","history.go(-1)");
	}
	$bakpath=$public_r['bakdbpath'];
	$path=$bakpath."/".$mypath;
	@include($path."/config.php");
	if(empty($b_table)){
		printerror("ErrorUrl","history.go(-1)");
	}
	$waitbaktime=(int)$_GET['waitbaktime'];
	if(empty($stime))
	{
		$stime=time();
	}
	$header="<?php
@include(\"../../inc/header.php\");
";
	$footer="
@include(\"../../inc/footer.php\");
?>";
	$btb=explode(",",$b_table);
	$count=count($btb);
	$t=(int)$t;
	$s=(int)$s;
	$p=(int)$p;
	//�ƥ�����
	if($t>=$count)
	{
		insert_dolog("dbname=".$b_dbname);//�ާ@��x
		$varmessage=$fun_r['BakSuccess']."<br><br>".$fun_r['TotalUseTime'].ToChangeUseTime($stime);
		$GLOBALS['varmessage']=$varmessage;
		printerror('VarMessage','ChangeDb.php'.hReturnEcmsHashStrHref2(1),0,1);
    }
	$dumpsql=Ebak_ReturnVer();
	//��ܼƾڮw
	$u=$empire->usequery("use `$b_dbname`");
	//�s�X
	if($b_dbchar=='auto')
	{
		if(empty($s))
		{
			$status_r=Ebak_GetTotal($b_dbname,$btb[$t]);
			$collation=Ebak_GetSetChar($status_r['Collation']);
			DoSetDbChar($collation);
			//�`�O����
			$num=$public_r[limittype]?-1:$status_r['Rows'];
		}
		else
		{
			$collation=$_GET['collation'];
			DoSetDbChar($collation);
			$num=(int)$alltotal;
		}
		$dumpsql.=Ebak_ReturnSetNames($collation);
	}
	else
	{
		DoSetDbChar($b_dbchar);
		if(empty($s))
		{
			//�`�O����
			if($public_r[limittype])
			{
				$num=-1;
			}
			else
			{
				$status_r=Ebak_GetTotal($b_dbname,$btb[$t]);
				$num=$status_r['Rows'];
			}
		}
		else
		{
			$num=(int)$alltotal;
		}
	}
	//�ƥ��ƾڮw���c
	if($b_stru&&empty($s))
	{
		$dumpsql.=Ebak_Returnstru($btb[$t],$b_strufour);
	}
	//���o�r�q��
	if(empty($fnum))
	{
		$return_fr=Ebak_ReturnTbfield($b_dbname,$btb[$t],$b_autofield);
		$fieldnum=$return_fr['num'];
		$noautof=$return_fr['autof'];
		$auf=$return_fr['auf'];
	}
	else
	{
		$fieldnum=$fnum;
		$noautof=$thenof;
	}
	//�۰��ѧO�ۼW��
	$aufval=(int)$aufval;
	if($b_autoauf==1&&$auf)
	{
		$sql=$empire->query("select * from `".$btb[$t]."` where ".$auf.">".$aufval." order by ".$auf." limit $b_bakline");
	}
	else
	{
		$sql=$empire->query("select * from `".$btb[$t]."` limit $s,$b_bakline");
	}
	//���㴡�J
	$inf='';
	if($b_beover==1)
	{
		$inf='('.Ebak_ReturnInTbfield($b_dbname,$btb[$t]).')';
	}
	//�Q���i��
	$hexf='';
	if($b_bakdatatype==1)
	{
		$hexf=Ebak_ReturnInStrTbfield($b_dbname,$btb[$t]);
	}
	$b=0;
	while($r=$empire->fetch($sql))
	{
		if($auf)
		{
			$lastaufval=$r[$auf];
		}
		$b=1;
		$s++;
		$dumpsql.="E_D(\"".$b_insertf." into `".$btb[$t]."`".$inf." values(";
		$first=1;
		for($i=0;$i<$fieldnum;$i++)
		{
			//���r�q
			if(empty($first))
			{
				$dumpsql.=',';
			}
			else
			{
				$first=0;
			}
			$myi=$i+1;
			if(!isset($r[$i])||strstr($noautof,','.$myi.','))
			{
				$dumpsql.='NULL';
			}
			else
			{
				$dumpsql.=Ebak_ReSqlFtext($r[$i],$b_bakdatatype,$myi,$hexf);
			}
		}
		$dumpsql.=");\");\r\n";
	}
	if(empty($b))
	{
		//�̫�@�ӳƥ�
		if(empty($p))
		{
			$p++;
			$sfile=$path."/".$btb[$t]."_".$p.".php";
			$dumpsql=$header.$dumpsql.$footer;
			WriteFiletext_n($sfile,$dumpsql);
		}
		Ebak_RepFilenum($p,$btb[$t],$path);
		$t++;
		$empire->free($sql);
		//�i�J�U�@�Ӫ�
		//echo $btb[$t-1].$fun_r['OneTableBakSuccess']."<script>self.location.href='phome.php?phome=BakExeT&s=0&p=0&t=$t&mypath=$mypath&stime=$stime".hReturnEcmsHashStrHref(0)."';</script>";

		echo"<meta http-equiv=\"refresh\" content=\"".$waitbaktime.";url=phome.php?phome=BakExeT&s=0&p=0&t=$t&mypath=$mypath&stime=$stime&waitbaktime=$waitbaktime".hReturnEcmsHashStrHref(0)."\">".$btb[$t-1].$fun_r['OneTableBakSuccess'];
		exit();
	}
	//�i�J�U�@��
	$p++;
	$sfile=$path."/".$btb[$t]."_".$p.".php";
	$dumpsql=$header.$dumpsql.$footer;
	WriteFiletext_n($sfile,$dumpsql);
	$empire->free($sql);
	//echo $fun_r['BakOneDataSuccess'].Ebak_EchoBakSt($btb[$t],$count,$t,$num,$s)."<script>self.location.href='phome.php?phome=BakExeT&s=$s&p=$p&t=$t&mypath=$mypath&alltotal=$num&thenof=$noautof&fieldnum=$fieldnum&auf=$auf&aufval=$lastaufval&stime=$stime".hReturnEcmsHashStrHref(0)."';</script>";

	echo"<meta http-equiv=\"refresh\" content=\"".$waitbaktime.";url=phome.php?phome=BakExeT&s=$s&p=$p&t=$t&mypath=$mypath&alltotal=$num&thenof=$noautof&fieldnum=$fieldnum&auf=$auf&aufval=$lastaufval&stime=$stime&waitbaktime=$waitbaktime&collation=$collation".hReturnEcmsHashStrHref(0)."\">".$fun_r['BakOneDataSuccess'].Ebak_EchoBakSt($btb[$t],$count,$t,$num,$s);
	exit();
}

//��X�ƥ��i�ױ�
function Ebak_EchoBakSt($tbname,$tbnum,$tb,$rnum,$r){
	$table=($tb+1).'/'.$tbnum;
	$record=$r;
	if($rnum!=-1)
	{
		$record=$r.'/'.$rnum;
	}
	?>
	<br><br>
	<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1">
		<tr><td height="25">Table Name&nbsp;:&nbsp;<b><?=$tbname?></b></td></tr>
		<tr><td height="25">Table&nbsp;:&nbsp;<b><?=$table?></b></td></tr>
		<tr><td height="25">Record&nbsp;:&nbsp;<b><?=$record?></b></td></tr>
	</table><br><br>
	<?php
}

//��X��_�i�ױ�
function Ebak_EchoReDataSt($tbname,$tbnum,$tb,$pnum,$p){
	$table=($tb+1).'/'.$tbnum;
	$record=$p.'/'.$pnum;
	?>
	<br><br>
	<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1">
		<tr><td height="25">Table Name&nbsp;:&nbsp;<b><?=$tbname?></b></td></tr>
		<tr><td height="25">Table&nbsp;:&nbsp;<b><?=$table?></b></td></tr>
		<tr><td height="25">File&nbsp;:&nbsp;<b><?=$record?></b></td></tr>
	</table><br><br>
	<?php
}

//���o���O����
function Ebak_GetTotal($dbname,$tbname){
	global $empire;
	/*
	$tr=$empire->fetch1("select count(*) AS total from ".$btb[$t]);
	$num=$tr[total];
	*/
	$tr=$empire->fetch1("SHOW TABLE STATUS LIKE '".$tbname."';");
	return $tr;
}

//��^�r�Ŷ�set
function Ebak_GetSetChar($char){
	global $empire;
	if(empty($char))
	{
		return '';
	}
	$r=$empire->fetch1("SHOW COLLATION LIKE '".$char."';");
	return $r['Charset'];
}

//��^���r�q�H��
function Ebak_ReturnTbfield($dbname,$tbname,$autofield){
	global $empire;
	$sql=$empire->query("SHOW FIELDS FROM `".$tbname."`");
	$i=0;//�r�q��
	$autof=",";//�h���ۼW�r�q�C��
	$f='';//�ۼW�r�q�W
	while($r=$empire->fetch($sql))
	{
		$i++;
		if(strstr($autofield,",".$tbname.".".$r[Field].","))
		{
			$autof.=$i.",";
	    }
		if($r['Extra']=='auto_increment')
		{
			$f=$r['Field'];
		}
    }
	$return_r['num']=$i;
	$return_r['autof']=$autof;
	$return_r['auf']=$f;
	return $return_r;
}

//��^���J�r�q
function Ebak_ReturnInTbfield($dbname,$tbname){
	global $empire;
	$sql=$empire->query("SHOW FIELDS FROM `".$tbname."`");
	$f='';
	$dh='';
	while($r=$empire->fetch($sql))
	{
		$f.=$dh.'`'.$r['Field'].'`';
		$dh=',';
    }
	return $f;
}

//��^�r�Ŧr�q
function Ebak_ReturnInStrTbfield($dbname,$tbname){
	global $empire;
	$sql=$empire->query("SHOW FIELDS FROM `".$tbname."`");
	$i=0;
	$f='';
	$dh='';
	while($r=$empire->fetch($sql))
	{
		$i++;
		if(!(stristr($r[Type],'char')||stristr($r[Type],'text')))
		{
			continue;
		}
		$f.=$dh.$i;
		$dh=',';
    }
	if($f)
	{
		$f=','.$f.',';
	}
	return $f;
}

//��^�r�q���e
function Ebak_ReSqlFtext($str,$bakdatatype,$i,$tbstrf){
	if($bakdatatype==1&&!empty($str)&&strstr($tbstrf,','.$i.','))
	{
		$restr='0x'.bin2hex($str);
	}
	else
	{
		$restr='\''.escape_str($str).'\'';
	}
	return $restr;
}

//��������
function Ebak_RepFilenum($p,$table,$path){
	if(empty($p))
	{$p=0;}
	$file=$path."/config.php";
	$text=ReadFiletext($file);
	$rep1="\$tb[".$table."]=0;";
	$rep2="\$tb[".$table."]=".$p.";";
	$text=str_replace($rep1,$rep2,$text);
	WriteFiletext_n($file,$text);
}

//����SQL
function E_D($sql){
	global $empire;
	$empire->query($sql);
}

//�إߪ�
function E_C($sql){
	global $empire;
	$empire->query(Ebak_AddDbchar($sql));
}

//�ରMysql4.0�榡
function Ebak_ToMysqlFour($query){
	$exp="ENGINE=";
	if(!strstr($query,$exp))
	{
		return $query;
	}
	$exp1=" ";
	$r=explode($exp,$query);
	//���o������
	$r1=explode($exp1,$r[1]);
	$returnquery=$r[0]."TYPE=".$r1[0];
	return $returnquery;
}

//---------------------��^�ƾڮw���c
function Ebak_Returnstru($table,$strufour){
	global $empire;
	$dumpsql.="E_D(\"DROP TABLE IF EXISTS `".$table."`;\");\r\n";
	//�]�m�޸�
	$usql=$empire->query("SET SQL_QUOTE_SHOW_CREATE=1;");
	//�ƾڪ����c
	$r=$empire->fetch1("SHOW CREATE TABLE `$table`;");
	$create=str_replace("\"","\\\"",$r[1]);
	//�ର4.0�榡
	if($strufour)
	{
		$create=Ebak_ToMysqlFour($create);
	}
	$dumpsql.="E_C(\"".$create."\");\r\n";
	return $dumpsql;
}

//��^�]�m�s�X
function Ebak_ReturnSetNames($char){
	if(empty($char))
	{
		return '';
	}
	$dumpsql="DoSetDbChar('".$char."');\r\n";
	return $dumpsql;
}

//�h���r�q�����s�X
function Ebak_ReplaceFieldChar($sql){
	global $ecms_config;
	if($ecms_config['db']['dbver']=='4.0'&&strstr($sql,' character set '))
	{
		$preg_str="/ character set (.+?) collate (.+?) /is";
		$sql=preg_replace($preg_str,' ',$sql);
	}
	return $sql;
}

//�[�s�X
function Ebak_AddDbchar($sql){
	global $ecms_config,$b_dbchar;
	//�[�s�X
	if($ecms_config['db']['dbver']>='4.1'&&!strstr($sql,'ENGINE=')&&($ecms_config['db']['setchar']||$b_dbchar)&&$b_dbchar!='auto')
	{
		$dbcharset=$b_dbchar?$b_dbchar:$ecms_config['db']['setchar'];
		$sql=Ebak_DoCreateTable($sql,$ecms_config['db']['dbver'],$dbcharset);
	}
	elseif($ecms_config['db']['dbver']=='4.0'&&strstr($sql,'ENGINE='))
	{
		$sql=Ebak_ToMysqlFour($sql);
	}
	//�h���r�q�����s�X
	$sql=Ebak_ReplaceFieldChar($sql);
	return $sql;
}

//�ت�
function Ebak_DoCreateTable($sql,$mysqlver,$dbcharset){
	$type=strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU","\\2",$sql));
	$type=in_array($type,array('MYISAM','HEAP'))?$type:'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU","\\1",$sql).
		($mysqlver>='4.1'?" ENGINE=$type DEFAULT CHARSET=$dbcharset":" TYPE=$type");
}

//��^���v�H��
function Ebak_ReturnVer()
{
	$string="
/*
		SoftName : EmpireBak
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

";
	return $string;
}

//�ഫ�j�p
function Ebak_ChangeSize($size){
	if($size<1024)
	{
		$str=$size." B";
	}
	elseif($size<1024*1024)
	{
		$str=round($size/1024,2)." KB";
	}
	elseif($size<1024*1024*1024)
	{
		$str=round($size/(1024*1024),2)." MB";
	}
	else
	{
		$str=round($size/(1024*1024*1024),2)." GB";
	}
	return $str;
}

//�ɤJ�ƾ�
function Ebak_ReData($add,$mypath,$userid,$username){
	global $empire,$public_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$mypath=RepPostStr($mypath,1);
	$add[mydbname]=RepPostStr($add[mydbname],1);
	if(empty($mypath)||empty($add[mydbname]))
	{printerror("EmptyDbnamePath","history.go(-1)");}
	$bakpath=$public_r['bakdbpath'];
	$path=$bakpath."/".$mypath;
	if(!file_exists($path))
	{
		printerror("DbPathNotExist","history.go(-1)");
    }
	@include($path."/config.php");
	if(empty($b_table))
	{
		printerror("BakCsError","history.go(-1)");
	}
	$waitbaktime=(int)$add['waitbaktime'];
	$btb=explode(",",$b_table);
	$nfile=$path."/".$btb[0]."_1.php?t=0&p=0&mydbname=$add[mydbname]&mypath=$mypath&waitbaktime=$waitbaktime".hReturnEcmsHashStrHref(0);
	Header("Location:$nfile");
	exit();
}

//�R���ƥ��ؿ�
function Ebak_DelBakpath($path,$userid,$username){
	global $public_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$path=RepPostStr($path,1);
	if(strstr($path,".."))
	{printerror("NotChangeDelPath","history.go(-1)");}
	if(!trim($path))
	{printerror("NotChangeDelPath","history.go(-1)");}
	$bakpath=$public_r['bakdbpath'];
	$delpath=$bakpath."/".$path;
	if(!file_exists($delpath))
	{
		printerror("ThisPathNotExist","history.go(-1)");
    }
	@include_once("../../class/delpath.php");
	$delpath=DelPath($delpath);
	//�ާ@��x
	insert_dolog("path=".$path);
	printerror("DelPathSuccess","ChangePath.php?change=".RepPostStr($_GET['change'],1).hReturnEcmsHashStrHref2(0));
}

//���]�ؿ�
function ZipFile($path,$zipname){
	global $public_r;
	$bakpath=$public_r['bakdbpath'];
	$bakzippath=$public_r['bakdbzip'];
	@include("../../class/phpzip.inc.php");
	$z=new PHPZip(); //�s�إߤ@��zip����
    $z->Zip($bakpath."/".$path,$bakzippath."/".$zipname); //�K�[���w�ؿ�
}

//�R�����Y�]
function Ebak_DelZip($file,$userid,$username){
	global $public_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$file=RepPostStr($file,1);
	if(strstr($file,".."))
	{printerror("FileNotExist","history.go(-1)");}
	if(empty($file))
	{
		printerror("FileNotExist","history.go(-1)");
    }
	$bakzippath=$public_r['bakdbzip'];
	$filename=$bakzippath."/".$file;
	if(!file_exists($filename))
	{
		printerror("FileNotExist","history.go(-1)");
	}
	DelFiletext($filename);
	//�ާ@��x
	insert_dolog("file=".$file);
	printerror("DelZipFileSuccess","history.go(-1)");
}

//���Y�ؿ�
function Ebak_Dozip($path,$userid,$username){
	global $public_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$path=RepPostStr($path,1);
	if(strstr($path,".."))
	{printerror("ThisPathNotExist","history.go(-1)");}
	if(empty($path))
	{
		printerror("ThisPathNotExist","history.go(-1)");
    }
	$bakpath=$public_r['bakdbpath'];
    $bakzippath=$public_r['bakdbzip'];
	$mypath=$bakpath."/".$path;
	if(!file_exists($mypath))
	{
		printerror("ThisPathNotExist","history.go(-1)");
	}
	$zipname=$path.".zip";
	ZipFile($path,$zipname);
	echo"<script>self.location.href='DownZip.php?f=$zipname&p=$path".hReturnEcmsHashStrHref2(0)."';</script>";
}

//��V��_����
function Ebak_PathGotoRedata($path,$userid,$username){
	global $public_r;
	//�����v��
	CheckLevel($userid,$username,$classid,"dbdata");
	$path=RepPostStr($path,1);
	if(strstr($path,".."))
	{printerror("NotChangeDelPath","history.go(-1)");}
	if(!trim($path))
	{printerror("NotChangeDelPath","history.go(-1)");}
	$bakpath=$public_r['bakdbpath'];
	$repath=$bakpath."/".$path;
	if(!file_exists($repath))
	{
		printerror("ThisPathNotExist","history.go(-1)");
    }
	@include $repath.'/config.php';
	Header("Location:ReData.php?mydbname=$b_dbname&mypath=$path".hReturnEcmsHashStrHref2(0));
	exit();
}
?>