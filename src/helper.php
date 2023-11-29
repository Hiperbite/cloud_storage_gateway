<?php

class helper{

  public static function dumFile($vinfo){
     $file=new \stdClass();
    if ($vinfo[0] !== "total") {
      $file->chmod = $vinfo[0];
      $file->num = $vinfo[1];
      $file->owner = $vinfo[2];
      $file->group = $vinfo[3];
      $file->size = $vinfo[4];
      $file->month = $vinfo[5];
      $file->day = $vinfo[6];
      $file->time = $vinfo[7];
      $file->name = $vinfo[8];
    }
  }

  public static function dumpListeFile($ftp_rawlist)
  {
   $files = array();
      foreach ($ftp_rawlist as $v) {
        $info = array();
        
        $vinfo = preg_split("/[\s]+/", $vfile, 9);
        
        $file=dumFile($vinfo);
        
        array_push($files,$file);
      }

    return $files;
  }
}
