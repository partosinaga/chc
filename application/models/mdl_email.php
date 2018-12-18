<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_email extends CI_Model{

    function sendEmailSmtp($to = array(), $cc = array(), $bcc = array(), $subject = '', $body = ''){
        $result = array();
        $result['success'] = true;
        $result['message'] = '';

        
        $config = Array(
            "protocol" 	=> "smtp",
            "smtp_host" => "mail.pakubuwono6.com",
            "smtp_port" => 587,
            "smtp_user" => "appdev@pakubuwono6.com",
            "smtp_pass" => "app123",
            "mailtype"  => "html",
            "charset"   => "utf-8",
            "wordwrap"	=> TRUE
        );
        

        /*$config = Array(
            "protocol" 	=> "smtp",
            "smtp_host" => "mail.dwijayahouse.com",
            "smtp_port" => 587,
            "smtp_user" => "appdev@dwijayahouse.com",
            "smtp_pass" => "app123",
            "mailtype"  => "html",
            "charset"   => "utf-8",
            "wordwrap"	=> TRUE
        );*/

        $this->load->library("email");

        $this->email->initialize($config);

        $this->email->from("appdev@pakubuwono6.com", "HALOKANG");
        if(count($to) > 0){
            $this->email->to($to);
        }
        if(count($cc) > 0){
            $this->email->cc($cc);
        }
        if(count($bcc) > 0){
            $this->email->bcc($bcc);
        }

        $this->email->subject($subject);
        $this->email->message($body);

        if($this->email->send()){
            $result['message'] = 'Success send email.';
        }
        else {
            $result['success'] = false;
            $result['message'] = $this->email->print_debugger();
        }

        return $result;

    }

}
?>