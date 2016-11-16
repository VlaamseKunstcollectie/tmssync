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

/**
 * CSVReader class
 *
 * A wrapper class around League\Csv\Reader. This class will read out CSV files
 * stored in app/files.
 *
 * @author Matthias Vandermaesen <matthias@colada.be>
 */
class CSVReader {
    /**
     * Representation of the filesystem.
     */
    private $filesystem;

    /**
     * The basepath of the filesystem.
     */
    private $basepath;

    /**
     * An instance of League\Csv\Reader
     */
    private $reader;

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
     * Get an initialized CSV reader for a particular file.
     *
     * This function initializes a League\Csv\Reader for an existing file stored
     * in app/files.
     *
     * @return League\Csv\Reader An initialized CSV reader
     */
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
