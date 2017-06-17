<?php
require_once 'class/UA_JSON.php';
#注册插件
RegisterPlugin("xnxf_aff","ActivePlugin_xnxf_aff");

function ActivePlugin_xnxf_aff() {
  // Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'aff_Page');
  Add_Filter_Plugin('Filter_Plugin_ViewAuto_Begin', 'aff_Page');
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
  $m   = array();
  if (preg_match($r, $url, $m) == 1) {
    // var_dump($m);
    // die();
    $article = new Post;
    $article->Type=ZC_POST_TYPE_PAGE;
    $article->IsLock = true;
    if (isset($m[1]) && $m[1] != ""){
      $article->Title = "欢迎访问本站";
      UA_JSON::Add($m[1]);
    }else{
      $article->Title = "获取推广链接";
      UA_JSON::Add();
    }
    $ip = GetGuestIP();
    $link = md5($ip);
    $link = $zbp->host . "aff/{$link}";
    $arrAff = array();
    $arrAff["host"] = $zbp->host;
    $arrAff["tip"] = aff_CFG("tip");
    $arrAff["link"] = $link;
    $arrAff["ip"] = $ip;
    $arrAff["click"] = GetVars('aff-click', 'COOKIE');
    $arrAff["time"] = GetVars('aff-time', 'COOKIE');
    $arrAff["ua"] = GetGuestAgent();
    $arrAff["copy"] = aff_CFG("copy") . "\n{$link}";
    $article->Content = file_get_contents(aff_Path("Table.html"));

    foreach ($arrAff as $key=>$value){
      $article->Content = str_replace("{{$key}}",$value,$article->Content);
    }

    $zbp->header.='<link rel="stylesheet" href="'.aff_Path("Table.css","host").'" />';
    $zbp->template->SetTags('title',$article->Title);
    $zbp->template->SetTags('article',$article);
    $zbp->template->SetTags('type','page');
    $zbp->template->SetTemplate($article->Template);
    $zbp->template->Display();
    die();
  }
  return;
}

function aff_Check(){
  return UA_JSON::Add();
}
function aff_CFG($name){
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

function InstallPlugin_xnxf_aff() {
  global $zbp;
  $fileLog = aff_Path("Log");
  if (!is_file($fileLog)) {
    @mkdir(dirname($fileLog));
    UA_JSON::Create();
    UA_JSON::Add();
  }
  if (!$zbp->HasConfig('xnxf_aff')) {
    $zbp->Config('xnxf_aff')->unit    = 10;
    $zbp->Config('xnxf_aff')->divide  = 3;
    $zbp->Config('xnxf_aff')->tip  = "分享下边链接给您的朋友，每三次点击将给您的当前访问增加一小时的权限提升！";
    $zbp->SaveConfig('xnxf_aff');
  }
}
function UninstallPlugin_xnxf_aff() {}

