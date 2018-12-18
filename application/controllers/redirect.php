<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redirect extends CI_Controller {

    public function unsupported_browser() {
        $this->load->view('layout/unsupported_browser');
    }
}

/* End of file redirect.php */
/* Location: ./application/controllers/redirect.php */