<?php
/**
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
 * Main class.
 *
 * This class registers the console comands for this application and bootstraps
 * the application on runtime.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class Main {
    /**
     * An instance of Symfony\Component\Console\Application
     */
    protected $app;

    /**
     * An instance of Flemishartcollection\TMSSync\Exporter
     */
    protected $exporter;

    /**
     * An instance of Flemishartcollection\TMSSync\Installer
     */
    protected $installer;

    /**
     * Constructor
     */
    public function __construct(Application $application) {
        $this->app = $application;
    }

    /**
     * Dependency method injection. Sets the exporter class.
     *
     * @param Flemishartcollection\TMSSync\Exporter $exporter The exporter.
     */
    public function setExporter(Exporter $exporter) {
        $this->exporter = $exporter;
    }

    /**
     * Dependency method injection. Sets the installer class.
     *
     * @param Flemishartcollection\TMSSync\Installer $installer The installer.
     */
    public function setInstaller(Installer $installer) {
        $this->installer = $installer;
    }

    /**
     * Bootstrap the application on runtime.
     */
    public function run() {
        $this->app->add($this->exporter);
        $this->app->add($this->installer);
        $this->app->run();
    }
}
