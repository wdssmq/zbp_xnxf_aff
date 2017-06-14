<?php
class UA_JSON
{
  // const REFERER_DEFAULT
  public static function FilePath()
  {
    return aff_Path("Log");
  }
  //UA_JSON::Read()
  public static function Read()
  {
    return json_decode(file_get_contents(UA_JSON::FilePath()));
  }
  //UA_JSON::Create()
  public static function Create()
  {
    $file = UA_JSON::FilePath();
    if (!is_file($file)) {
      if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
      }
      file_put_contents($file, '{"Guests":{"8.8.8.8":{"Time":1487651979,"Agent":"XNXF","Click":0}}}');
    }
  }
  // UA_JSON::Add()
  public static function Add($aff = "")
  {
    $result = false;
    $data   = UA_JSON::Read();
    $Guests =& $data->Guests;
    $IP    = md5(GetGuestIP());
    $Agent = GetGuestAgent();
    if (!isset($Guests->$IP)) {
      if($aff!=""&&isset($Guests->$aff)){
        $Guests->$aff->Click ++;
        $Guests->$aff->Time += ($Guests->$aff->Click % 3 + 1) * 600; 
        $result            = true;
      }
      $Guests->$IP        = json_decode('{"Time":' . time() . '}');
      $Guests->$IP->Agent = $Agent;
      $Guests->$IP->Click    = 0;
    } else {
      if ($Guests->$IP->Agent === $Agent && $Guests->$IP->Time>time()) {
        setcookie("aff-time", date("Y-m-d H:i:s",$Guests->$IP->Time));
        $result            = true;
      }
    }
    // if($aff!=""&&isset($Guests->$aff)){
      // $Guests->$aff->Click ++;
      // $Guests->$aff->Time += ($Guests->$aff->Click % 3 + 1) * 600; 
      // $result            = true;
    // }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
    return $result;
  }
  // UA_JSON::Del()
  public static function Del()
  {
    $data = UA_JSON::Read();
    $Guests =& $data->Guests;
    foreach ($Guests as $k => $v) {
      if ($v->Time < strtotime("-7 day")) {
        unset($Guests->$k);
      }
    }
    $file = UA_JSON::FilePath();
    file_put_contents($file, json_encode($data));
  }
}
?>