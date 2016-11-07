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

/**
 * Main class
 */
class Main {
    protected $app;

    protected $exporter;

    public function __construct(Application $application, Exporter $exporter) {
        $this->app = $application;
        $this->exporter = $exporter;
    }

    public function run() {
        $this->app->add($this->exporter);
        $this->app->run();
    }
}
