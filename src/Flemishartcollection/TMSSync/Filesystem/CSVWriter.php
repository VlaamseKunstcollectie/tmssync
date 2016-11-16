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
use League\Csv\Writer;

class CSVWriter {
    private $filesystem;

    private $basepath;

    private $writer;

    private $file;

    public function __construct() {
        $this->basepath = __DIR__.'/../../../../app/files';
        $adapter = new Local($this->basepath);
        $this->filesystem = new Filesystem($adapter);
    }

    public function createCSV($name) {
        $this->file = sprintf("%s.csv", $name);
        if ($this->filesystem->has($this->file)) {
            $this->filesystem->delete($this->file); // remove file if it exists
        }
        $this->file = sprintf("%s/%s.csv", $this->basepath, $name);
        $this->writer = Writer::createFromPath($this->file, "w");
        $this->writer->setOutputBOM(Writer::BOM_UTF8);
    }

    public function setHeader($header) {
        $this->writer->insertOne($header);
    }

    public function insertOne($row) {
        $this->writer->insertOne($row);
    }
}
