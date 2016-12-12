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

/**
 * CSVWriter class
 *
 * A wrapper class around League\Csv\Writer. This class will write CSV files
 * to the app/files directory.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class CSVWriter {
    /**
     * Representation of the filesystem.
     */
    private $filesystem;

    /**
     * The basepath of the filesystem.
     */
    private $basepath;

    /**
     * An instance of League\Csv\Writer
     */
    private $writer;

    private $file;

    /**
     * Constructor
     */
    public function __construct() {
        $this->basepath = __DIR__.'/../../../../app/files';
        $adapter = new Local($this->basepath);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * Create a CSV file
     *
     * Create a CSV file in app/files and make it ready to write data to.
     *
     * @param string $name The name of the CSV file
     */
    public function createCSV($name) {
        $this->file = sprintf("%s.csv", $name);
        if ($this->filesystem->has($this->file)) {
            $this->filesystem->delete($this->file); // remove file if it exists
        }
        $this->file = sprintf("%s/%s.csv", $this->basepath, $name);
        $this->writer = Writer::createFromPath($this->file, "w");
        $this->writer->appendStreamFilter('convert.iconv.ISO-8859-1/UTF-8');
        $this->writer->setOutputBOM(Writer::BOM_UTF8);
    }

    /**
     * Set the header of the CSV file.
     *
     * @param array $header The header of the CSV file.
     */
    public function setHeader($header) {
        $this->writer->insertOne($header);
    }

    /**
     * Insert a signle row into the CSV file.
     *
     * @param array $row A single row of data to be inserted into the CSV file.
     */
    public function insertOne($row) {
        $this->writer->insertOne($row);
    }
}
