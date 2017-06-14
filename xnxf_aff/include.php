<?php
require_once 'class/UA_JSON.php';
#注册插件
RegisterPlugin("xnxf_aff","ActivePlugin_xnxf_aff");

function ActivePlugin_xnxf_aff() {
  // Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'aff_Page');
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
    $ip = GetGuestIP();
    $link = md5($ip);
    $link = $zbp->host . "aff/{$link}";
    $article = new Post;
    $article->Type=ZC_POST_TYPE_PAGE;
    $article->IsLock = true;
    $article->Content = file_get_contents(aff_Path("Table"));
    if (isset($m[1]) && $m[1] != ""){
      $article->Title = "欢迎访问本站";
      UA_JSON::Add($m[1]);
    }else{
      $article->Title = "获取推广链接";
      UA_JSON::Add();
    }
    $article->Content = str_replace('{aff-link}',$link,$article->Content);
    $article->Content = str_replace('{aff-ip}',$ip,$article->Content);
    $article->Content = str_replace('{aff-ua}',GetGuestAgent(),$article->Content);
    $article->Content = str_replace('{aff-time}',GetVars('aff-time', 'COOKIE'),$article->Content);
    $zbp->template->SetTags('title',$article->Title);
    $zbp->template->SetTags('article',$article);
    $zbp->template->SetTags('type','page');
    $zbp->template->SetTemplate($article->Template);
    $zbp->template->Display();
    die();
  }
}

// function aff_CFG($name){
  // switch ($file) {
    // case "urlPartern":
      // return "/(?J)^aff\/?([^\/]*)$/";
    // break;
  // }
// }

function aff_Path($file, $t = "path")
{
  global $zbp;
  $result = $zbp->$t . "zb_users/plugin/xnxf_aff/";
  switch ($file) {
    case "Log":
      return $result . "cache/log.json";
      break;
    case "Table":
      return $result . "var/table.html";
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
  $fileLog = aff_Path("Log");
  if (!is_file($fileLog)) {
    @mkdir(dirname($fileLog));
    UA_JSON::Create();
    UA_JSON::Add();
  }
}
function UninstallPlugin_xnxf_aff() {}

