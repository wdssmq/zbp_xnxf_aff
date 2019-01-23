<?php
require_once 'class/UA_JSON.php';
//注册插件
RegisterPlugin("xnxf_aff", "ActivePlugin_xnxf_aff");
function ActivePlugin_xnxf_aff()
{
  Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'aff_Hide');

  Add_Filter_Plugin('Filter_Plugin_ViewAuto_Begin', 'aff_Page');
}
function aff_Hide(&$template)
{
  global $zbp;
  $article = $template->GetTags('article');
  if (!aff_CFG("hideOn") || $zbp->user->ID > 0 || aff_Check())
    return;
  aff_GenPage($article);
  $tips = str_replace('{$host}', $zbp->host, aff_CFG("hide"));
  $tips = "<div class=\"aff-form\">{$tips}</div>";
  $article->Content = $tips . $article->Content;
}
function aff_Page($url)
{
  global $zbp;
  if ($zbp->cookiespath === substr($url, 0, strlen($zbp->cookiespath))) {
    $url = substr($url, strlen($zbp->cookiespath));
  }
  $url = trim(urldecode($url), '/');
  $r = "/^aff\/?([^\/]*)$/";
  // echo $r;
  // echo "<br />";
  // echo $url;
  $m = array();
  if (preg_match($r, $url, $m) == 1) {
    // var_dump($m);
    // die();
    $post = null;
    aff_GenPage($post, $m);
    $zbp->template->Display();
    die();
  }
  return;
}
function aff_GenPage(&$article = null, $m = array())
{
  global $zbp;
  $geetestOn = $zbp->CheckPlugin('xnxf_geetest') ? true : false;
  $isAff = false;
  if (empty($article)) {
    $isAff = true;
    $article = new Post;
    $article->Type = ZC_POST_TYPE_PAGE;
    $article->IsLock = true;
    $zbp->template->SetTemplate($article->Template);
    $zbp->template->SetTags('type', 'page');
  }

  $ip = UA_JSON::GuestIP();
  $link = md5($ip);
  $link = $zbp->host . "aff/{$link}";
  $arrAff = array();
  $arrAff["geetest"] = "";
  $arrAff["geetest-tip"] = "";
  if ($isAff && isset($m[1]) && $m[1] != "") {
    $article->Title = "欢迎访问本站";
    if ($geetestOn) {
      $arrAff["geetest"] = '<div class="fade" id="aff-geetest"><p>请完成验证</p><div  id="xnxf-geetest" style="margin: 8px 0;"></div></div>';
      setcookie("aff-last", $m[1], time() + 300, $zbp->cookiespath);
    } else {
      UA_JSON::Add($m[1]);
    }
  } else if ($isAff) {
    $article->Title = "获取推广链接";
    UA_JSON::Add();
  }
  $arrAff["host"] = $zbp->host;
  $arrAff["tip"] = aff_CFG("tip");
  // if ($geetestOn)
  // $arrAff["geetest-tip"]    = "完成人机验证既为有效";
  $arrAff["link"] = $link;
  $arrAff["ip"] = $ip;
  $arrAff["click"] = GetVars('aff-click', 'COOKIE');
  $arrAff["time"] = GetVars('aff-time', 'COOKIE');
  $arrAff["ua"] = GetGuestAgent();
  $arrAff["copy"] = aff_CFG("copy") . "\n{$link}";
  $article->Content = file_get_contents(aff_Path("Table.html"));
  foreach ($arrAff as $key => $value) {
    $article->Content = str_replace("{{$key}}", $value, $article->Content);
  }
  $zbp->header .= '<link rel="stylesheet" href="' . aff_Path("Table.css", "host") . '" />';
  $table = explode("------", $article->Content);
  $article->Content = $table[0];
  $zbp->template->SetTags('title', $article->Title);
  $zbp->template->SetTags('article', $article);
  $zbp->Config('xnxf_aff')->hideOn = false;
  if ($geetestOn) {
    foreach ($GLOBALS['hooks']['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
      $fpreturn = $fpname($zbp->template);
    }
    $zbp->footer = str_replace("<!--gtplug-->", '<script src="' . aff_Path("Table.js", "host") . '" type="text/javascript"></script><!--gtplug-->', $zbp->footer);
  }
  $zbp->footer .= $table[1];
}
function aff_Check()
{
  return UA_JSON::Add();
}
function aff_CFG($name)
{
  global $zbp;
  return $zbp->Config('xnxf_aff')->$name;
}
function aff_Path($file, $t = "path")
{
  global $zbp;
  $result = $zbp->$t . "zb_users/plugin/xnxf_aff/";
  switch ($file) {
    case "Log":
      return $result . "cache/log.json";
      break;
    case "Table.html":
      return $result . "var/table.html";
      break;
    case "Table.css":
      return $result . "var/table.css";
      break;
    case "Table.js":
      return $result . "var/table.js";
      break;
    case "Tips":
      return $result . "var/tips.html";
      break;
    case "Main":
      return $result . "main.php";
      break;
    default:
      return $result;
  }
}
function InstallPlugin_xnxf_aff()
{
  global $zbp;
  $fileLog = aff_Path("Log");
  if (!is_file($fileLog)) {
    @mkdir(dirname($fileLog));
    UA_JSON::Create();
    UA_JSON::Add();
  }
  if (!$zbp->HasConfig('xnxf_aff')) {
    $zbp->Config('xnxf_aff')->unit = 10;
    $zbp->Config('xnxf_aff')->divide = 3;
    $zbp->Config('xnxf_aff')->tip = "分享下边链接给您的朋友，每三次点击将给您的当前访问增加一小时的权限提升！";
    $zbp->Config('xnxf_aff')->copy = "世纪末彼岸花丛中的死之结界：";
    $zbp->Config('xnxf_aff')->hide = '此处内容已隐藏，请<a href="{$host}?Login" title="登录">登录</a>或<b>宣传本站链接</b>以获取阅读权限！';
    $zbp->SaveConfig('xnxf_aff');
  }
}
function UninstallPlugin_xnxf_aff()
{
}
