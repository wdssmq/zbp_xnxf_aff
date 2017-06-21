<?php
require '../../../zb_system/function/c_system_base.php';
$zbp->Load();
if (!$zbp->CheckPlugin('xnxf_aff')) {
  $zbp->ShowError(48);
  die();
}
if (!$zbp->CheckPlugin('xnxf_geetest')) {
  $zbp->ShowError(48);
  die();
}
$result = geetest_verify_base();
if ($result == "ok") {
  UA_JSON::Add(GetVars('aff-last', 'COOKIE'));
  // ScriptError("验证成功");
} else {
  ScriptError($result);
}
?>