<?php
namespace util;

class Util{
    public static function serverRedirect(string $url){
        if (empty($url)) return false;

        header("Location: {$url}");
    }

    public static function clientRedirectAfterAlert(string $url, string $msg = ''){
        if (empty($url)) return false;


        $script = "<script>";

        if (!empty($msg)) $script .= "alert('{$msg}')";

        $script .= "document.location.href = {$url}";
        $script .= "</script>";
    }
}
?>

