<?php
if(!defined('InEmpireCMS'))
{
	exit();
}
?>
<script language="javascript" src="js/var.js"></script>
<script language="javascript" src="js/player.js"></script>
<script language="javascript" src="js/timer.js"></script>

<HTML>
<HEAD>
<TITLE><?=$r[title]?> --- �C�鼽��</TITLE>
<META HTTP-EQUIV="Expires" CONTENT="0">
<link rel="stylesheet" href="js/player.css">
<script language="javascript">
window.resizeTo( 375 , 421 );
window.moveTo( 100 , 100 );
window.focus()
</script>
<SCRIPT language=javascript>
function click() {
if (event.button==2) {
alert('�藍�_�A�z�Q������H')
}
}
document.onmousedown=click
</SCRIPT>

<script language="javascript" src="js/wmplayer.js"></script>
<script language="javascript">Init();</script>
<BODY id=thisbody  bgcolor="#000000" topMargin=0 leftMargin=0 rightMargin=0 bottomMargin=0 style="scroll:no; overflow: hidden;" ondragstart="self.event.returnValue=false" onselectstart="self.event.returnValue=false" onmousedown="WindowMove_Start()" onmouseup="WindowMove_End()" onmousemove="WindowMove()">
<object id="min" type="application/x-oleobject" classid="clsid:adb880a6-d8ff-11cf-9377-00aa003b7a11" style="{visibility:hidden}"><param name="Command" value="Minimize"></object>
<!--���񾹥D�ɭ�-->
<DIV id="myplayPanel">
  <TABLE border=0 cellPadding=0 cellSpacing=0 width="362">
    <TR><TD width="11"><img border="0" src="images/top_l.gif" width="11" height="37"></td><td background="images/top_bg.gif" width="340"></TD><td background="images/top_bg.gif" NOWRAP valign="center">
      </TD><td width="11"><img border="0" src="images/top_r.gif" width="11" height="37"></td>
    </TR>
    <TR>
      <td background="images/center_bgl.gif" width="11">&nbsp;</td>
      <TD bgcolor="#000000" colspan="2" width="340" height="250">
        <DIV id="bufimg" style="HEIGHT: 100%; POSITION: absolute; TOP: 37px; VISIBILITY: visible; WIDTH: 100%; Z-INDEX: 4;left:11px"><img border="0" src="images/bufimg.gif" width="340" height="250"></DIV>
        <DIV id="MP" style="VISIBILITY: hidden; Z-INDEX: -1">
        <OBJECT classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" id=Player codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" standby="�[�� Microsoft Windows Media Player �ե�..." type="application/x-oleobject" width="340" height="250">
         <PARAM NAME="AutoStart" VALUE="1">
         <PARAM NAME="AutoSize" VALUE="0">
          <PARAM NAME="AutoRewind" VALUE="-1">
          <PARAM NAME="AnimationAtStart" VALUE="0">
          <PARAM NAME="ClickToPlay" VALUE="0">
          <PARAM NAME="EnableContextMenu" VALUE="0">
          <!--�C����-->
          <PARAM NAME="FileName" VALUE="">
          <!------------->
          <PARAM NAME="ShowControls" VALUE="0">
          <PARAM NAME="ShowAudioControls" VALUE="0">
          <PARAM NAME="ShowPositionControls" VALUE="0">
          <PARAM NAME="ShowStatusBar" VALUE="0">
          <PARAM NAME="ShowTracker" VALUE="0">
          <PARAM NAME="Volume" VALUE="-100">
        </OBJECT></DIV>
      </TD>
      <td background="images/center_bgr.gif" width="11">&nbsp;</td>
    </TR>
    </table>
<!--Control Pancel Start-->
<DIV id="mycontrolPanel">
<!--------��m����------------->
<DIV id="myadvControl"><div id="info"></div></DIV>
<DIV id="myPosBar" onmousedown="SetPos_Start()" onmousemove="SetPos()" onmouseup="SetPos_End()"><IMG alt="�����m" border=0  src="images/control_bar.gif" width="29" style="cursor:hand" height="12">
</DIV>
<table border="0" width="362" cellspacing="0" cellpadding="0" height="17">
    <tr>
      <td background="images/control1.gif">
      �@
      </td>
    </tr>
</table>
<!--------�ɶ����------------->
<table border="0" width="362" cellspacing="0" cellpadding="0" height="50" background="images/control20.gif">
    <tr>
      <td width="100%" height="24">
       </td>
    </tr>
    <tr>
      <td width="100%" height="26">
         <img src="images/clock_l.gif" width="245" height="13"><img src="js/cb.gif" name="a" width="11" height="13"><img src="js/cb.gif" name="b" width="11" height="13"><img src="js/colon.gif" name="c" width="11" height="13"><img src="js/cb.gif" name="d" width="11" height="13"><img src="js/cb.gif" name="e" width="11" height="13"><img src="js/colon.gif" name="f" width="11" height="13"><img src="js/cb.gif" name="g" width="11" height="13"><img src="js/cb.gif" name="h" width="11" height="13">
       </td>
    </tr>
</table>
<!--------�n������------------->
  <DIV id="myBalBar" onmousedown="SetBal_Start()" onmousemove="SetBal()" onmouseup="SetBal_End()"><IMG alt="���q����" border=0 src="images/bar.gif" width="11" height="8" style="cursor:hand"></DIV>
  <DIV id="myVolBar" onmousedown="SetVol_Start()" onmousemove="SetVol()" onmouseup="SetVol_End()"><IMG alt="���q�ո`" border=0 src="images/bar.gif" width="11" height="8" style="cursor:hand"></DIV>
  <table border="0" width="362" cellspacing="0" cellpadding="0">
    <tr>
      <td><img border="0" src="images/control3_01.gif" width="42" height="35"></td>
      <td><img border="0" src="images/control3_02.gif" width="23" height="35" title="����" onclick="Play()" name="playpause" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_03.gif" width="23" height="35" title="�Ȱ�" onclick="Pause()" name="playpause" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_04.gif" width="24" height="35" name="stop" title="����" onclick="Stop()" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_05.gif" width="5" height="35"></td>
      <td><img border="0" src="images/control3_06.gif" width="23" height="35" alt="�}�l��m" title="�}�l��m" onclick="SetPosition(0)" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_07.gif" width="24" height="35" alt="������m" title="������m" onclick="SetPosition(-1)" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_08.gif" width="5" height="35"></td>
      <td><img border="0" src="images/control3_09.gif" width="24" height="35" name="fullscreen" alt="���̹�" title="���̹�" onClick="FullScreen()" style="cursor:hand"></td>
      <td><img border="0" src="images/control3_10.gif" width="169" height="35"></td>
    </tr>
  </table>
<!--Control Panel End-->
<!--------���ű���------------->
<DIV id="myadvPanel">
</DIV>
<!--panel table end-->
<!--�ƥ�B�z-->
<SCRIPT FOR="Player" EVENT="Buffering(bStart)" LANGUAGE="JavaScript">
        ShowBuffering(bStart);
        if(bStart){
                ShowBufferingPro();
        }
</SCRIPT>
<SCRIPT FOR="window" EVENT="onresize()" language="javascript">
        document.all.Player.width=document.body.clientWidth-20;
        document.all.Player.height=document.all.Player.width*0.75;
</SCRIPT>
<SCRIPT FOR="Player" EVENT="Error( )" LANGUAGE="JScript">
        window.clearInterval(m_timer);
        info.innerHTML="�A�Ⱦ������A�еy�ԦA�աK�K"
</SCRIPT>

<SCRIPT FOR="Player" EVENT="OpenStateChange(lOldState, lNewState)" LANGUAGE="JScript">
        switch(lNewState){
        case 0:
                info.innerHTML="�A�Ⱦ������A�s�������K�K";
                window.clearInterval(m_timer);break;
        case 4:
                info.innerHTML="���b�s���A�Ⱦ��K�K";break;
        case 5:
                info.innerHTML="���b���}���K�K";break;
        case 6:
                info.innerHTML="�ǳƼ���K�K";StreamMedia();break;
        }
</SCRIPT>

<script language='javascript'>window.document.all.Player.Open('<?=$url?>');</script>
<script language="javascript">
        if(BROD==1) myPosBar.style.visibility = "hidden";
        StartStreamMonitor();
</script>

</body>
</html>