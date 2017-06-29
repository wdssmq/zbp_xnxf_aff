<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('xnxf_aff')) {$zbp->ShowError(48);die();}

$blogtitle='站点推广';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

InstallPlugin_xnxf_aff();
UA_JSON::Del();
if(GetVars('act','GET')=='save'){
  $zbp->Config('xnxf_aff')->unit = round($_POST['unit']);
  $zbp->Config('xnxf_aff')->divide = round($_POST['divide']);
  $zbp->Config('xnxf_aff')->tip = $_POST['tip'];
  $zbp->Config('xnxf_aff')->copy = $_POST['copy'];
  $zbp->Config('xnxf_aff')->hide = $_POST['hide'];
  $zbp->Config('xnxf_aff')->hideOn = $_POST['hideOn'];
  $zbp->SaveConfig('xnxf_aff');
  $zbp->SetHint('good');
  Redirect('./main.php');
}

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"></div>
  <div id="divMain2">
    <form name="arr-form" action="main.php?act=save" method="post">
    <table style="width: 100%;">
      <tr>
        <th width="7%" height="32">项目</th>
        <th>内容</th>
        <th width="51%">说明</th>
      </tr>
      <tr>
        <td>时间基数</td>
        <td><input value="<?php echo aff_CFG("unit") ?>" name="unit" id="unit" type="number" min="1" />分钟</td>
        <td>每次有效点击增加的时长为<b><span class="js-unit">时间基数</span></b>的整数倍</td>
      </tr>
      <tr>
        <td>计数周期</td>
        <td><input value="<?php echo aff_CFG("divide") ?>" name="divide" id="divide" type="number" min="1" /></td>
        <td>每个周期内，第<b>n</b>次点击增加<b><span class="js-unit">时间基数</span>*n</b>分钟，<b>1≤<b>n</b>≤<span class="js-divide"></span></b>。</td>
      </tr>
      <tr>
        <td>提示语</td>
        <td>
          <textarea onmouseover="this.select();" name="tip" id="tip" style="width: 96%;padding: 3px;" rows="3"><?php echo aff_CFG("tip") ?></textarea>
        </td>
        <td></td>
      </tr>
      <tr>
        <td>转发文案</td>
        <td>
          <textarea placeholder="世纪末彼岸花丛中的死之结界:" onmouseover="this.select();" name="copy" id="copy" style="width: 96%;padding: 3px;" rows="3"><?php echo aff_CFG("copy") ?></textarea>
        </td>
        <td>会自动附加aff链接</td>
      </tr>
      <tr>
      	<td>邀请页入口</td>
      	<td><input onclick="that = this;setTimeout(function(){that.select()},150);" style="width: 96%;" type="text" onchange="this.value='<?php echo $zbp->host?>aff/'" value="<?php echo $zbp->host?>aff/" onmouseover="this.select();" /></td>
      	<td>此项仅为输出，编辑无效</td>
      </tr>
      <tr>
      	<td>全局启用</td>
      	<td><textarea onmouseover="this.select();" name="hide" id="hide" style="width: 96%;padding: 3px;" rows="3"><?php echo aff_CFG("hide") ?></textarea></td>
      	<td><input type="text" name="hideOn" id="hideOn" value="<?php echo aff_CFG("hideOn") ?>" class="checkbox"/> 会对全部文章正文启用隐藏，可使用下方代码进行自定义判断！</td>
      </tr>
      <tr>
      	<td colspan="1"><input type="submit" value="提交" /></td>
      	<td colspan="2"></td>
      </tr>
    </table>
    </form>
    <h3>使用说明</h3>
<pre>{if $zbp->CheckPlugin('xnxf_aff') && aff_Check()}
//原始内容
{else}
<?php echo TransferHTML('<a href="{$host}aff/" title="获取推广链接">获取推广链接</a>','[html-format]') ?>"
{/if}</pre>
  </div>
  <script type="text/javascript">
    $(function(){
      function modfiSpan(){
        $(".js-unit").each(function(){
          $(this).html($("#unit").val());
        });
        $(".js-divide").each(function(){
          $(this).html($("#divide").val());
        });
      }
      $("#unit,#divide").change(modfiSpan);
      modfiSpan();
    });
  </script>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>