<?php
//�ͦ��Ҧ����e����
function ReNewsHtml($start,$classid,$from,$retype,$startday,$endday,$startid,$endid,$tbname,$havehtml){
	global $empire,$public_r,$class_r,$fun_r,$dbtbpre,$etable_r;
	$tbname=RepPostVar($tbname);
	if(empty($tbname)||!eCheckTbname($tbname))
	{
		printerror("ErrorUrl","history.go(-1)");
    }
	$start=(int)$start;
	//��ID
	if($retype)
	{
		$startid=(int)$startid;
		$endid=(int)$endid;
		$add1=$endid?' and id>='.$startid.' and id<='.$endid:'';
    }
	else
	{
		$startday=RepPostVar($startday);
		$endday=RepPostVar($endday);
		$add1=$startday&&$endday?' and truetime>='.to_time($startday.' 00:00:00').' and truetime<='.to_time($endday.' 23:59:59'):'';
    }
	//�����
	$classid=(int)$classid;
	if($classid)
	{
		$where=empty($class_r[$classid][islast])?ReturnClass($class_r[$classid][sonclass]):"classid='$classid'";
		$add1.=' and '.$where;
    }
	//���ͦ�
	$add1.=ReturnNreInfoWhere();
	//�O�_���ƥͦ�
	$updatehavehtml='havehtml=0';
	if($havehtml!=1)
	{
		$add1.=' and havehtml=0';
		$updatehavehtml='';
	}
	//�u��
	$yhadd='';
	$yhid=$etable_r[$tbname][yhid];
	$yhvar='rehtml';
	if($yhid)
	{
		$yhadd=ReturnYhSql($yhid,$yhvar,1);
	}
	$b=0;
	$sql=$empire->query("select id,classid from {$dbtbpre}ecms_".$tbname."_index where ".$yhadd."id>$start".$add1." and checked=1 order by id limit ".$public_r[renewsnum]);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$new_start=$r[id];
		if($class_r[$r[classid]][showdt]==2)
		{
			continue;
		}
		GetHtml($r['classid'],$r['id'],'',0,1);//�ͦ��H�����
	}
	if(empty($b))
	{
		//��s���A
		if($yhadd.$add1=='')
		{
			$empire->query("update {$dbtbpre}ecms_".$tbname."_index set havehtml=1 where havehtml=0 and checked=1");
			$empire->query("update {$dbtbpre}ecms_".$tbname." set havehtml=1 where havehtml=0");
		}
		else
		{
			if($updatehavehtml)
			{
				$updatehavehtml=' and '.$updatehavehtml;
			}
			if($yhadd&&$add1)
			{
				$truewhereindex=$yhadd.substr($add1,5).' and checked=1';
				$truewhere=$yhadd.substr($add1,5);
			}
			elseif($yhadd&&!$add1)
			{
				$truewhereindex=$yhadd.'checked=1';
				$truewhere=substr($yhadd,0,-5);
			}
			else
			{
				$truewhereindex=substr($add1,5).' and checked=1';
				$truewhere=substr($add1,5);
			}
			$empire->query("update {$dbtbpre}ecms_".$tbname."_index set havehtml=1 where ".$truewhereindex.$updatehavehtml);
			$empire->query("update {$dbtbpre}ecms_".$tbname." set havehtml=1 where ".$truewhere.$updatehavehtml);
		}
		echo "<link rel=\"stylesheet\" href=\"../data/images/css.css\" type=\"text/css\"><center><b>".$tbname.$fun_r[ReTableIsOK]."!</b></center>";
		db_close();
		$empire=null;
		exit();
	}
	echo"<link rel=\"stylesheet\" href=\"../data/images/css.css\" type=\"text/css\"><meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReNewsHtml&tbname=$tbname&classid=$classid&start=$new_start&from=".urlencode($from)."&retype=$retype&startday=$startday&endday=$endday&startid=$startid&endid=$endid&havehtml=$havehtml&reallinfotime=".ehtmlspecialchars($_GET['reallinfotime']).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReNewsHtmlSuccess]."(ID:<font color=red><b>".$new_start."</b></font>)";
	exit();
}

//��s�Ҧ��C��
function ReListHtml_all($start,$do,$from){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	if($do=="all")
	{
		insert_dolog("");//�ާ@��x
		printerror("ReClassidAllSuccess",$from);
    }
	//���
	$sql=$empire->query("select classid,classtempid,islast,islist from {$dbtbpre}enewsclass where classid>$start and nreclass=0 order by classid limit ".$public_r[relistnum]);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		if(!$r[islast])//�j���
		{
			if($r[islist]==1)
			{
				ListHtml($r[classid],$ret_r,3);
			}
			elseif($r[islist]==3)//��ظj�w�H��
			{
				ReClassBdInfo($r[classid]);
			}
			else
			{
				$classtemp=$r[islist]==2?GetClassText($r[classid]):GetClassTemp($r['classtempid']);
				NewsBq($r[classid],$classtemp,0,0);
			}
		}
		else//�l���
		{
			ListHtml($r[classid],$ret_r,0);
		}
		$end_classid=$r[classid];
	}
	if(empty($b))
	{
		echo $fun_r[ReListNewsSuccess]."<script>self.location.href='ecmschtml.php?enews=ReListHtml_all&start=0&from=".urlencode($from).hReturnEcmsHashStrHref(0)."&do=all';</script>";
		exit();
    }
	//echo $fun_r[OneReListNewsSuccess]."(ID:<font color=red><b>".$end_classid."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReListHtml_all&start=$end_classid&do=class&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReListHtml_all&start=$end_classid&do=class&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReListNewsSuccess]."(ID:<font color=red><b>".$end_classid."</b></font>)";
	exit();
}

//��s�M�D�C��
function ReZtListHtml_all($start,$do,$from){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$time=time();
	if($do=="all")
	{
		insert_dolog("");//�ާ@��x
		printerror("ReZtidAllSuccess",$from);
    }
	elseif($do=="ztc")//��s�M�D�l��
	{
		$zcsql=$empire->query("select cid from {$dbtbpre}enewszttype where cid>$start and (endtime=0 or endtime>$time) order by cid limit ".$public_r['relistnum']);
		while($c_r=$empire->fetch($zcsql))
		{
			$b=1;
			ListHtmlIndex($c_r['cid'],$ret_r,1);
			$end_classid=$c_r['cid'];
		}
		if(empty($b))
		{
			echo $fun_r[ReZtcListNewsSuccess]."<script>self.location.href='ecmschtml.php?enews=ReZtListHtml_all&start=0&do=all&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
			exit();
		}
		echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReZtListHtml_all&start=$end_classid&do=ztc&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReZtcListNewsSuccess]."(ZtID:<font color=red><b>".$end_classid."</b></font>)";
		exit();
	}
	$zsql=$empire->query("select ztid from {$dbtbpre}enewszt where ztid>$start and (endtime=0 or endtime>$time) order by ztid limit ".$public_r['relistnum']);
	while($z_r=$empire->fetch($zsql))
	{
		$b=1;
		ListHtmlIndex($z_r['ztid'],$ret_r,0);
		$end_classid=$z_r['ztid'];
	}
	if(empty($b))
	{
		echo $fun_r[ReZtListNewsSuccess]."<script>self.location.href='ecmschtml.php?enews=ReZtListHtml_all&start=0&do=ztc&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
		exit();
	}
	echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReZtListHtml_all&start=$end_classid&do=zt&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReZtListNewsSuccess]."(ZtID:<font color=red><b>".$end_classid."</b></font>)";
	exit();
}

//��s��ӱM�D
function DoReZtListHtml($ztid,$ecms=0){
	global $empire,$dbtbpre,$public_r;
	ListHtmlIndex($ztid,$ret_r,0);
	//��s�M�D�l��
	if($ecms==1)
	{
		$csql=$empire->query("select cid from {$dbtbpre}enewszttype where ztid='$ztid'");
		while($cr=$empire->fetch($csql))
		{
			ListHtmlIndex($cr['cid'],$ret_r,1);
		}
	}
}

//��s���D����
function ReTtListHtml_all($start,$do,$from){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$tsql=$empire->query("select typeid from {$dbtbpre}enewsinfotype where typeid>$start and listdt=0 order by typeid limit ".$public_r['relistnum']);
	while($t_r=$empire->fetch($tsql))
	{
		$b=1;
		ListHtml($t_r['typeid'],$ret_r,5);
		$end_classid=$t_r['typeid'];
	}
	if(empty($b))
	{
		insert_dolog("");//�ާ@��x
		printerror("ReTtidAllSuccess",$from);
	}
	echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReTtListHtml_all&start=$end_classid&do=tt&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReTtListNewsSuccess]."(ZtID:<font color=red><b>".$end_classid."</b></font>)";
	exit();
}

//��s�Ҧ�js
function ReAllNewsJs($start,$do,$from){
	global $empire,$public_r,$fun_r,$dbtbpre;
	$start=(int)$start;
	$line=$public_r[relistnum];
	$b=0;
	if($do=="all")
	{
		$pr=$empire->fetch1("select hotnum,newnum,goodnum,hotplnum,firstnum,jstempid from {$dbtbpre}enewspublic limit 1");
		$jstemptext=GetTheJstemp($pr['jstempid']);//js�ҪO
		//��s����js
		GetNewsJs($classid,$pr[newnum],$pr[sub_new],$pr[newshowdate],3,$jstemptext);
		GetNewsJs($classid,$pr[hotnum],$pr[sub_hot],$pr[hotshowdate],4,$jstemptext);
		GetNewsJs($classid,$pr[goodnum],$pr[sub_good],$pr[goodshowdate],5,$jstemptext);
		GetNewsJs($classid,$pr[hotplnum],$pr[sub_hotpl],$pr[hotplshowdate],10,$jstemptext);
		GetNewsJs($classid,$pr[firstnum],$pr[sub_first],$pr[firstshowdate],13,$jstemptext);
		insert_dolog("");//�ާ@��x
		printerror("ReAllJsSuccess",$from);
	}
	elseif($do=="tt")//��s���D����js
	{
		//$from=urlencode($from);
		$sql=$empire->query("select typeid,newline,hotline,goodline,hotplline,firstline,jstempid from {$dbtbpre}enewsinfotype where typeid>$start and nrejs=0 order by typeid limit $line");
		while($r=$empire->fetch($sql))
		{
			$jstemptext=GetTheJstemp($r[jstempid]);//js�ҪO
			$b=1;
			GetNewsJs($r[typeid],$r[newline],$r[newstrlen],$r[newshowdate],25,$jstemptext);
			GetNewsJs($r[typeid],$r[hotline],$r[hotstrlen],$r[hotshowdate],26,$jstemptext);
			GetNewsJs($r[typeid],$r[goodline],$r[goodstrlen],$r[goodshowdate],27,$jstemptext);
			GetNewsJs($r[typeid],$r[hotplline],$r[hotplstrlen],$r[hotplshowdate],28,$jstemptext);
			GetNewsJs($r[typeid],$r[firstline],$r[firststrlen],$r[firstshowdate],29,$jstemptext);
			$newstart=$r[typeid];
		}
		//��s����
		if(empty($b))
		{
			echo $fun_r[ReTtNewsJsSuccess]."<script>self.location.href='ecmschtml.php?enews=ReAllNewsJs&do=all&start=0&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
			exit();
	    }
		//echo $fun_r[OneReTtNewsJsSuccess]."(ZtID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReAllNewsJs&do=tt&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
		echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReAllNewsJs&do=tt&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReTtNewsJsSuccess]."(ZtID:<font color=red><b>".$newstart."</b></font>)";
		exit();
	}
	else//��s���js
	{
		//$from=urlencode($from);
		$sql=$empire->query("select classid,newline,hotline,goodline,hotplline,firstline,jstempid from {$dbtbpre}enewsclass where classid>$start and nrejs=0 and wburl='' order by classid limit $line");
		while($r=$empire->fetch($sql))
		{
			$jstemptext=GetTheJstemp($r[jstempid]);//js�ҪO
			$b=1;
			GetNewsJs($r[classid],$r[newline],$r[newstrlen],$r[newshowdate],0,$jstemptext);
			GetNewsJs($r[classid],$r[hotline],$r[hotstrlen],$r[hotshowdate],1,$jstemptext);
			GetNewsJs($r[classid],$r[goodline],$r[goodstrlen],$r[goodshowdate],2,$jstemptext);
			GetNewsJs($r[classid],$r[hotplline],$r[hotplstrlen],$r[hotplshowdate],9,$jstemptext);
			GetNewsJs($r[classid],$r[firstline],$r[firststrlen],$r[firstshowdate],12,$jstemptext);
			$newstart=$r[classid];
		}
		//��s����
		if(empty($b))
		{
			echo $fun_r[ReClassNewsJsSuccess]."<script>self.location.href='ecmschtml.php?enews=ReAllNewsJs&do=tt&start=0&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
			exit();
	    }
		//echo $fun_r[OneReClassNewsJsSuccess]."(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReAllNewsJs&do=class&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
		echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=ReAllNewsJs&do=class&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneReClassNewsJsSuccess]."(ID:<font color=red><b>".$newstart."</b></font>)";
		exit();
	}
}

//��s�̷s�峹�P�����峹
function ReHot_NewNews(){
	global $empire,$dbtbpre;
	$public_r=$empire->fetch1("select hotnum,newnum,goodnum,hotplnum,firstnum,jstempid from {$dbtbpre}enewspublic limit 1");
	$jstemptext=GetTheJstemp($public_r['jstempid']);//���ojs�ҪO
	GetNewsJs($classid,$public_r[newnum],$public_r[sub_new],$public_r[newshowdate],3,$jstemptext);
	GetNewsJs($classid,$public_r[hotnum],$public_r[sub_hot],$public_r[hotshowdate],4,$jstemptext);
	GetNewsJs($classid,$public_r[goodnum],$public_r[sub_good],$public_r[goodshowdate],5,$jstemptext);
	GetNewsJs($classid,$public_r[hotplnum],$public_r[sub_hotpl],$public_r[hotplshowdate],10,$jstemptext);
	GetNewsJs($classid,$public_r[firstnum],$public_r[sub_first],$public_r[firstshowdate],13,$jstemptext);
	insert_dolog("");//�ާ@��x
	printerror("ReNewHotSuccess","history.go(-1)");
}

//��s�M�D
function ReZtHtml($ztid,$ecms=0){
	global $class_zr;
	$ztid=(int)$ztid;
	if(!$ztid)
	{
		printerror("NotChangeReZtid","history.go(-1)");
	}
	DoReZtListHtml($ztid,$ecms);
	insert_dolog("ztid=$ztid");//�ާ@��x
	printerror("ReZtidSuccess","history.go(-1)");
}

//��s���D����
function ReTtHtml($typeid){
	global $class_tr;
	$typeid=(int)$typeid;
	if(!$typeid)
	{
		printerror("NotChangeReTtid","history.go(-1)");
	}
	ListHtml($typeid,$ret_r,5);
	insert_dolog("typeid=$typeid");//�ާ@��x
	printerror("ReTtidSuccess","history.go(-1)");
}

//��s������
function ReSingleJs($classid,$doing=0){
	global $empire,$dbtbpre;
	$classid=(int)$classid;
	//��s���
	if($doing==0)
	{
		$r=$empire->fetch1("select classid,newline,hotline,goodline,hotplline,firstline,jstempid from {$dbtbpre}enewsclass where classid='$classid'");
		$jstemptext=GetTheJstemp($r[jstempid]);//js�ҪO
		GetNewsJs($r[classid],$r[newline],$r[newstrlen],$r[newshowdate],0,$jstemptext);
		GetNewsJs($r[classid],$r[hotline],$r[hotstrlen],$r[hotshowdate],1,$jstemptext);
		GetNewsJs($r[classid],$r[goodline],$r[goodstrlen],$r[goodshowdate],2,$jstemptext);
		GetNewsJs($r[classid],$r[hotplline],$r[hotplstrlen],$r[hotplshowdate],9,$jstemptext);
		GetNewsJs($r[classid],$r[firstline],$r[firststrlen],$r[firstshowdate],12,$jstemptext);
	}
	elseif($doing==1)//��s���D����js
	{
		$r=$empire->fetch1("select typeid,newline,hotline,goodline,hotplline,firstline,jstempid from {$dbtbpre}enewsinfotype where typeid='$classid'");
		$jstemptext=GetTheJstemp($r[jstempid]);//js�ҪO
		GetNewsJs($r[typeid],$r[newline],$r[newstrlen],$r[newshowdate],25,$jstemptext);
		GetNewsJs($r[typeid],$r[hotline],$r[hotstrlen],$r[hotshowdate],26,$jstemptext);
		GetNewsJs($r[typeid],$r[goodline],$r[goodstrlen],$r[goodshowdate],27,$jstemptext);
		GetNewsJs($r[typeid],$r[hotplline],$r[hotplstrlen],$r[hotplshowdate],28,$jstemptext);
		GetNewsJs($r[typeid],$r[firstline],$r[firststrlen],$r[firstshowdate],29,$jstemptext);
    }
	else
	{}
	insert_dolog("");//�ާ@��x
	printerror("ReJsSuccess","history.go(-1)");
}

//��q�ͦ��ʺA����
function ReDtPage($userid,$username){
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"changedata");
	GetPlTempPage();//���צC���ҪO
	GetPlJsPage();//����JS�ҪO
	ReCptemp();//����O�ҪO
	GetSearch();//�T�j������ҪO
	GetPrintPage();//���L�ҪO
	GetDownloadPage();//�U���a�}����
	ReGbooktemp();//�d���O�ҪO
	ReLoginIframe();//�n�����A�ҪO
	ReSchAlltemp();//�����j���ҪO
	//�ާ@��x
	insert_dolog("");
	printerror("ReDtPageSuccess","history.go(-1)");
}

//��q��s�۩w�q����
function ReUserpageAll($start=0,$from,$userid,$username){
	global $empire,$public_r,$fun_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$sql=$empire->query("select id,path,pagetext,title,pagetitle,pagekeywords,pagedescription,tempid from {$dbtbpre}enewspage where id>$start order by id limit ".$public_r['reuserpagenum']);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$newstart=$r[id];
		ReUserpage($r[id],$r[pagetext],$r[path],$r[title],$r[pagetitle],$r[pagekeywords],$r[pagedescription],$r[tempid]);
	}
	//����
	if(empty($b))
	{
		//�ާ@��x
	    insert_dolog("");
		printerror("ReUserpageAllSuccess",$from);
	}
	echo $fun_r['OneReUserpageSuccess']."(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReUserpageAll&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//��q��s�۩w�q�H���C��
function ReUserlistAll($start=0,$from,$userid,$username){
	global $empire,$public_r,$fun_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$sql=$empire->query("select listid,pagetitle,filepath,filetype,totalsql,listsql,maxnum,lencord,listtempid,pagekeywords,pagedescription from {$dbtbpre}enewsuserlist where listid>$start order by listid limit ".$public_r['reuserlistnum']);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$newstart=$r[listid];
		ReUserlist($r,"");
	}
	//����
	if(empty($b))
	{
		//�ާ@��x
	    insert_dolog("");
		printerror("ReUserlistAllSuccess",$from);
	}
	echo $fun_r['OneReUserlistSuccess']."(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReUserlistAll&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//��q��s�۩w�qJS
function ReUserjsAll($start=0,$from,$userid,$username){
	global $empire,$public_r,$fun_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$sql=$empire->query("select jsid,jsname,jssql,jstempid,jsfilename from {$dbtbpre}enewsuserjs where jsid>$start order by jsid limit ".$public_r['reuserjsnum']);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$newstart=$r[jsid];
		ReUserjs($r,"");
	}
	//����
	if(empty($b))
	{
		//�ާ@��x
	    insert_dolog("");
		printerror("ReUserjsAllSuccess",$from);
	}
	echo $fun_r['OneReUserjsSuccess']."(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReUserjsAll&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//��q��s�H�����
function ReSpAll($start=0,$from,$userid,$username){
	global $empire,$public_r,$fun_r,$dbtbpre;
	$start=(int)$start;
	$b=0;
	$sql=$empire->query("select spid,varname,refile,spfile,spfileline,spfilesub from {$dbtbpre}enewssp where refile=1 and spid>$start order by spid limit ".$public_r['reuserpagenum']);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$newstart=$r['spid'];
		DoSpReFile($r,0);
	}
	//����
	if(empty($b))
	{
		//�ާ@��x
	    insert_dolog("");
		printerror("ReSpAllSuccess",$from);
	}
	echo $fun_r['OneReSpSuccess']."(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReSpAll&start=$newstart&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//�ͦ���H�����
function ReSp($add,$userid,$username,$ecms=0){
	global $empire,$dbtbpre;
	//�ާ@�v��
	if($ecms==0)
	{
		CheckLevel($userid,$username,$classid,"sp");
	}
	$spid=$add['spid'];
	$count=count($spid);
	if(!$count)
	{
		printerror("EmptyReSpid","history.go(-1)");
    }
	for($i=0;$i<$count;$i++)
	{
		$spid[$i]=(int)$spid[$i];
		if(empty($spid[$i]))
		{
			continue;
		}
		DoSpReFile($r,$spid[$i]);
	}
	//�ާ@��x
	insert_dolog("");
	printerror("ReSpSuccess",$_SERVER['HTTP_REFERER']);
}

//��V�B�z���
function GoReListHtmlMore($classid,$gore,$from,$ecms=0){
	global $empire,$class_r;
	$count=count($classid);
	if($count==0)
	{
		printerror("EmptyReListHtmlMoreId","history.go(-1)");
    }
	$cid="";
	for($i=0;$i<$count;$i++)
	{
		if($i==0)
		{
			$fh="";
		}
		else
		{
			$fh=",";
		}
		$cid.=$fh.$classid[$i];
	}
	//���
	if(empty($gore))
	{
		$phome="ReListHtmlMore";
	}
	elseif($gore==2)//���D����
	{
		$phome="ReListTtHtmlMore";
	}
	else//�M�D
	{
		$phome="ReListZtHtmlMore";
	}
	echo"<script>self.location.href='ecmschtml.php?enews=$phome&classid=$cid&from=".urlencode($from).hReturnEcmsHashStrHref(0)."&ecms=$ecms';</script>";
	exit();
}

//��s�h�C��
function ReListHtmlMore($start,$classid,$from){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$classid=eReturnInids($classid);
	if(empty($classid))
	{
		printerror("ErrorUrl",$from);
    }
	$b=0;
	$sql=$empire->query("select classid,classtempid,islast,islist from {$dbtbpre}enewsclass where classid>$start and classid in(".$classid.") order by classid limit ".$public_r[relistnum]);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		//�j���
		if(!$r[islast])
		{
			if($r[islist]==1)
			{
				ListHtml($r[classid],$ret_r,3);
			}
			elseif($r[islist]==3)//��ظj�w�H��
			{
				ReClassBdInfo($r[classid]);
			}
			else
			{
				$classtemp=$r[islist]==2?GetClassText($r[classid]):GetClassTemp($r['classtempid']);
				NewsBq($r[classid],$classtemp,0,0);
			}
		}
		//�l���
		else
		{
			ListHtml($r[classid],$ret_r,0);
		}
		$end_classid=$r[classid];
	}
	if(empty($b))
	{
		//�ާ@��x
		insert_dolog("");
		printerror("ReClassidAllSuccess",$from);
    }
	echo $fun_r[OneReListNewsSuccess]."(ID:<font color=red><b>".$end_classid."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReListHtmlMore&start=$end_classid&classid=$classid&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//��s�h�M�D�C��
function ReListZtHtmlMore($start,$classid,$from,$ecms=0){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$classid=eReturnInids($classid);
	if(empty($classid))
	{
		printerror("ErrorUrl",$from);
    }
	$b=0;
	//��s�M�D
	$zsql=$empire->query("select ztid from {$dbtbpre}enewszt where ztid>$start and ztid in (".$classid.") order by ztid limit ".$public_r[relistnum]);
    while($z_r=$empire->fetch($zsql))
	{
		$b=1;
		DoReZtListHtml($z_r[ztid],$ecms);
		$end_classid=$z_r[ztid];
    }
	if(empty($b))
	{
		//�ާ@��x
		insert_dolog("");
		printerror("ReZtidAllSuccess",$from);
    }
    echo $fun_r[OneReZtListNewsSuccess]."(ZtID:<font color=red><b>".$end_classid."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReListZtHtmlMore&start=$end_classid&classid=$classid&from=".urlencode($from).hReturnEcmsHashStrHref(0)."&ecms=$ecms';</script>";
    exit();
}

//��s�h���D����
function ReListTtHtmlMore($start,$classid,$from){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$start;
	$classid=eReturnInids($classid);
	if(empty($classid))
	{
		printerror("ErrorUrl",$from);
    }
	$b=0;
	//��s���D����
	$tsql=$empire->query("select typeid from {$dbtbpre}enewsinfotype where typeid>$start and typeid in (".$classid.") order by typeid limit ".$public_r[relistnum]);
    while($t_r=$empire->fetch($tsql))
	{
		$b=1;
		ListHtml($t_r[typeid],$ret_r,5);
		$end_classid=$t_r[typeid];
    }
	if(empty($b))
	{
		//�ާ@��x
		insert_dolog("");
		printerror("ReTtidAllSuccess",$from);
    }
    echo $fun_r[OneReTtListNewsSuccess]."(ZtID:<font color=red><b>".$end_classid."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReListTtHtmlMore&start=$end_classid&classid=$classid&from=".urlencode($from).hReturnEcmsHashStrHref(0)."';</script>";
    exit();
}

//�ͦ���H��
function ReSingleInfo($userid,$username){
	global $empire,$public_r,$class_r,$dbtbpre;
	if($_GET['classid'])
	{
		$classid=(int)$_GET['classid'];
		$id=$_GET['id'];
	}
	else
	{
		$classid=(int)$_POST['classid'];
		$id=$_POST['id'];
	}
	if(empty($classid))
	{
		printerror('ErrorUrl','history.go(-1)');
	}
	$count=count($id);
	if(empty($count))
	{
		printerror("NotReInfoid","history.go(-1)");
	}
	for($i=0;$i<$count;$i++)
	{
		$id[$i]=intval($id[$i]);
		$add.="id='$id[$i]' or ";
    }
	$add=substr($add,0,strlen($add)-4);
	$sql=$empire->query("select * from {$dbtbpre}ecms_".$class_r[$classid][tbname]." where ".$add);
	while($r=$empire->fetch($sql))
	{
		GetHtml($r['classid'],$r['id'],$r,1);//�ͦ��H�����
	}
	//�ާ@��x
	insert_dolog("classid=".$classid);
	printerror("ReSingleInfoSuccess",$_SERVER['HTTP_REFERER']);
}

//��_��إؿ�
function ReClassPath($start=0){
	global $empire,$public_r,$dbtbpre;
	$start=(int)$start;
	$sql=$empire->query("select classid,classpath,islast from {$dbtbpre}enewsclass where wburl='' and classid>$start order by classid limit ".$public_r[relistnum]);
	$b=0;
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$newstart=$r[classid];
		$returnpath=FormatClassPath($r[classpath],$r[islast]);
		echo "Create Path:".$returnpath." success!<br>";
    }
	//��_�M�D�ؿ�
	if(empty($b))
	{
		$zsql=$empire->query("select ztid,ztpath from {$dbtbpre}enewszt order by ztid");
		while($zr=$empire->fetch($zsql))
		{
			CreateZtPath($zr[ztpath]);
		}
		//��_���D�����ؿ�
		$tsql=$empire->query("select typeid,tpath from {$dbtbpre}enewsinfotype order by typeid");
		while($tr=$empire->fetch($tsql))
		{
			CreateInfoTypePath($tr['tpath']);
		}
	}
	if(empty($b))
	{
		//�ާ@��x
	    insert_dolog("");
		printerror("ReClassPathSuccess","ReHtml/ChangeData.php".hReturnEcmsHashStrHref2(1));
	}
	echo"(ID:<font color=red><b>".$newstart."</b></font>)<script>self.location.href='ecmschtml.php?enews=ReClassPath&start=$newstart".hReturnEcmsHashStrHref(0)."';</script>";
	exit();
}

//�إ���إؿ�
function FormatClassPath($classpath,$islast){
	$r=explode("/",$classpath);
	$returnpath="";
	for($i=0;$i<count($r);$i++)
	{
		if($i>0)
		{
			$returnpath.="/".$r[$i];
		}
		else
		{
			$returnpath.=$r[$i];
		}
		CreateClassPath($returnpath);
	}
	return $returnpath;
}

//��s����
function ReIndex(){
	$indextemp=GetIndextemp();//���o�ҪO
	NewsBq($classid,$indextemp,1,0);
	insert_dolog("");//�ާ@��x
	printerror("ReIndexSuccess","history.go(-1)");
}

//��s��ثH����
function UpdateClassInfosAll($add){
	global $empire,$public_r,$fun_r,$class_r,$dbtbpre;
	$start=(int)$add['start'];
	$from=$add['from'];
	$b=0;
	//���
	$sql=$empire->query("select classid from {$dbtbpre}enewsclass where classid>$start and islast=1 order by classid limit ".$public_r[relistnum]);
	while($r=$empire->fetch($sql))
	{
		$b=1;
		$end_classid=$r['classid'];
		ResetClassInfos($r['classid']);
	}
	if(empty($b))
	{
		insert_dolog('');//�ާ@��x
		printerror('UpdateClassInfosAllSuccess',$from);
    }
	echo"<meta http-equiv=\"refresh\" content=\"".$public_r['realltime'].";url=ecmschtml.php?enews=UpdateClassInfosAll&start=$end_classid&from=".urlencode($from).hReturnEcmsHashStrHref(0)."\">".$fun_r[OneUpdateClassInfosSuccess]."(ID:<font color=red><b>".$end_classid."</b></font>)";
	exit();
}
?>