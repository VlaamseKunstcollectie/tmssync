<?php
/*
  * This file is part of the TMS Sync package.
 *
 * (c) Matthias Vandermaesen <matthias@colada.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Flemishartcollection\TMSSync\Filesystem;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Csv\Reader;

class CSVReader {
    private $filesystem;

    private $basepath;

    private $reader;

    private $file;

    public function __construct() {
        $this->basepath = __DIR__.'/../../../../app/files';
        $adapter = new Local($this->basepath);
        $this->filesystem = new Filesystem($adapter);
    }

    public function get($name) {
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $this->file = sprintf("%s.csv", $name);
        if ($this->filesystem->has($this->file)) {
            $this->file = sprintf("%s/%s.csv", $this->basepath, $name);
        }
        $this->file = sprintf("%s/%s.csv", $this->basepath, $name);
        return Reader::createFromPath($this->file);
    }
}
