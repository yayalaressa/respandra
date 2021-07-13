<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System extends AdminController {

    public $updater;

    public function __construct() 
    {
        parent::__construct();
        
        $this->updater = new \Kanti\HubUpdater(array(
            "holdTime" => 10,//time(seconds) the Cached-Information will be used
            "name" => 'yayalaressa/respandra',//Repository to watch
            "branch" => 'master',//wich branch to watch
            "cache" => 'temp/',//were to put the caching stuff
            "save" => FCPATH . 'cache/',//there to put the downloaded Version[default ./]
            "prerelease" => true,//accept prereleases?
        ));

    }

    public function index()
    {
        if ($this->updater->able()) {
            $info = $this->updater->getNewestInfo();
            $this->theme->render('admin/updater', array(
                'title' => 'Updater',
                'heading' => 'Your system a wait upgrade!',
                'is_role' => 'admin',
                'breadcrumb' => 'update system',
                'button' => 'Update to ' . $info['tag_name'],
                'update' => $info,
                'url' => site_url() . 'admin/updater/system/do_update'
            ));
        } else {
            $info = $this->updater->getCurrentInfo();
            $this->theme->render('admin/updater', array(
                'title' => 'Updater',
                'heading' => 'Your system is already the latest version.',
                'is_role' => 'admin',
                'breadcrumb' => 'update system',
                'button' => 'Read me',
                'update' => $info,
                'url' => 'https://yayalaressa.github.io/respandra/'
            ));
        }   
    }

    public function do_update()
    {
        if($this->updater->able()) {
            $this->updater->update();
            redirect(site_url() . 'admin/updater/system');
        } else {
            redirect(site_url() . 'admin/updater/system');
        }
    }


}

