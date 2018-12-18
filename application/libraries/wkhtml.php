<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require './vendor/autoload.php';
use Knp\Snappy\Pdf;

class Wkhtml {
    public function __construct() {
        $CI =& get_instance();

        //$CI->load->library('user_agent');
        $exist = strpos(strtolower(PHP_OS),'win');
        //var_dump($exist);
        if ($exist === false) {
            $wkhtml_path = WKHTML_LINUX;
        }else{
            $wkhtml_path = WKHTML_WINDOWS;
        }
        //echo 'OS : ' . PHP_OS . ' => ' . $wkhtml_path . ' => ' . $exist;
        //exit;
        $snappy = new Pdf($wkhtml_path);
        //$snappy = new Pdf();
        //$snappy->setBinary('/usr/bin/wkhtmltopdf.sh');
        //$snappy->setOption('javascript-delay', 500);

        $CI->snappy = $snappy;
    }
}