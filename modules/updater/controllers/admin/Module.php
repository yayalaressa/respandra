<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module extends AdminController {

    public $updater;

    public function __construct() 
    {
        parent::__construct();
        $this->updater = new \Kanti\HubUpdater(array(
            "cacheFile" => "moduleInfo.json",//name of the InformationCacheFile(in cacheDir)
            "holdTime" => 10, //time(seconds) the Cached-Information will be used
            "versionFile" => "moduleVersion.json",//name of the InstalledVersionInformation is safed(in cacheDir)
            "name" => 'yayalaressa/respandra-modules', //Repository to watch
            "prerelease" => true //accept prereleases?
        ));

    }

    public function index()
    {
        if ($this->updater->able()) {
            $info = $this->updater->getNewestInfo();
            $this->theme->render('admin/updater', array(
                'title' => 'Updater',
                'heading' => 'Your module a wait update!',
                'is_role' => 'admin',
                'breadcrumb' => 'Update module',
                'button' => 'Update to ' . $info['tag_name'],
                'update' => $info,
                'url' => site_url() . 'admin/updater/module/do_update'
            ));
        } else {
            $info = $this->updater->getCurrentInfo();
            $this->theme->render('admin/updater', array(
                'title' => 'Updater',
                'heading' => 'Your module is already the latest version.',
                'is_role' => 'admin',
                'breadcrumb' => 'Update module',
                'button' => 'Official repository',
                'update' => $info,
                'url' => 'https://yayalaressa.github.io/respandra-modules/'
            ));
        }   
    }

    public function do_update()
    {
        if($this->updater->able()) {
            $this->updater->update();
            redirect(site_url() . 'admin/updater/module');
        } else {
            redirect(site_url() . 'admin/updater/module');
        }
    }


}

