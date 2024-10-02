<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class MY_Controller extends CI_Controller 
{
  public function cekLogin()
  {
    if (!$this->session->userdata('auth')) {
      redirect('ValidationProcess');
    }
  }
  
  public function getMenu($menu)
  {
    $this->load->database('default');
    $this->load->model('MainPage_model');

    $auth         = $this->session->userdata('auth');

    $menumapping  = $this->MainPage_model->getMenuMapping($auth['user_group_level'], $menu);

    if(empty($menumapping)){
      $accesmenu = 0;
    } else {
      $accesmenu = 1;
    }

    return $accesmenu;
  }
 
  public function accessMenu($menu)
  {
    $accesmenu = $this->getMenu($menu);
 
    if ($accesmenu !== 1) redirect('MainPage');
  }
 
}