<?php
/*
 * This file is part of the TMS Sync package.
 *
 * (c) Matthias Vandermaesen <matthias@colada.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Flemishartcollection\TMSSync;

use Symfony\Component\Console\Application;
use Flemishartcollection\TMSSync\Exporter;
use Flemishartcollection\TMSSync\Installer;

/**
 * Main class
 */
class Main {
    protected $app;

    protected $exporter;

    protected $installer;

    public function __construct(Application $application) {
        $this->app = $application;
    }

    public function setExporter(Exporter $exporter) {
        $this->exporter = $exporter;
    }

    public function setInstaller(Installer $installer) {
        $this->installer = $installer;
    }

    public function run() {
        $this->app->add($this->exporter);
        $this->app->add($this->installer);
        $this->app->run();
    }
}
