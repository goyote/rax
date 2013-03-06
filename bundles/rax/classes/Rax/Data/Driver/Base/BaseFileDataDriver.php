<?php

namespace Rax\Data\Driver\Base;

use Rax\Data\Driver\DriverInterface;
use Rax\Data\Driver\FileDataDriver;
use Rax\Helper\Arr;
use Rax\Helper\Php;
use Rax\Mvc\Cfs;
use Rax\Mvc\Exception;

/**
 * Data bank file driver.
 *
 * @author    Gregorio Ramirez <goyocode@gmail.com>
 * @copyright Copyright (c) Gregorio Ramirez <goyocode@gmail.com>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD
 */
class BaseFileDataDriver implements DriverInterface
{
    /**
     * @var string
     */
    protected $scanDir;

    /**
     * @var Cfs
     */
    protected $cfs;

    /**
     * @var bool
     */
    protected $canMergeData = false;

    /**
     * @var string
     */
    protected $saveFile;

    /**
     * @var string
     */
    protected $saveFileExtension;

    /**
     * @var bool
     */
    public $canSave = true;

    /**
     * Returns a new chainable instance.
     *
     * @return FileDataDriver
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @param string $scanDir
     * @param Cfs    $cfs
     */
    public function __construct($scanDir = null, Cfs $cfs = null)
    {
        $this->scanDir = $scanDir;
        $this->cfs     = $cfs;
    }

    /**
     * Sets the name of the directory holding the data files.
     *
     * @param string $scanDir
     *
     * @return FileDataDriver
     */
    public function setScanDir($scanDir)
    {
        $this->scanDir = $scanDir;

        return $this;
    }

    /**
     * Returns the directory holding the data files.
     *
     * @return string
     */
    public function getScanDir()
    {
        return $this->scanDir;
    }

    /**
     * Sets the Cfs instance.
     *
     * @param Cfs $cfs
     *
     * @return FileDataDriver
     */
    public function setCfs($cfs)
    {
        $this->cfs = $cfs;

        return $this;
    }

    /**
     * Returns the Cfs instance.
     *
     * @return Cfs
     */
    public function getCfs()
    {
        return $this->cfs;
    }

    /**
     * Sets whether the new data replaces or merges the old data.
     *
     * @param bool $canMergeData
     *
     * @return FileDataDriver
     */
    public function setCanMergeData($canMergeData)
    {
        $this->canMergeData = (bool) $canMergeData;

        return $this;
    }

    /**
     * Returns whether the new data replaces or merges the old data.
     *
     * @return bool
     */
    public function getCanMergeData()
    {
        return $this->canMergeData;
    }

    /**
     * Sets the output file path.
     *
     * @param string $file
     *
     * @return FileDataDriver
     */
    public function setSaveFile($file)
    {
        $this->saveFileExtension = substr($file, strrpos($file, '<name>') + strlen('<name>') + 1);
        $this->saveFile          = $file;

        return $this;
    }

    /**
     * Returns the output file path.
     *
     * @return string
     */
    public function getSaveFile()
    {
        return $this->saveFile;
    }

    /**
     * Sets the output file extension.
     *
     * @param string $saveFileExtension
     *
     * @return FileDataDriver
     */
    public function setSaveFileExtension($saveFileExtension)
    {
        $this->saveFileExtension = $saveFileExtension;

        return $this;
    }

    /**
     * Returns the output file extension.
     *
     * @return string
     */
    public function getSaveFileExtension()
    {
        return $this->saveFileExtension;
    }

    /**
     * Sets whether file saving is enabled or not.
     *
     * @param boolean $canSave
     *
     * @return FileDataDriver
     */
    public function setCanSave($canSave)
    {
        $this->canSave = (bool) $canSave;

        return $this;
    }

    /**
     * Returns whether file saving is enabled or not.
     *
     * @return boolean
     */
    public function getCanSave()
    {
        return $this->canSave;
    }

    /**
     * {@inheritdoc}
     */
    public function load($key)
    {
        if (!$files = $this->cfs->findFiles($this->scanDir, $key)) {
            throw new Exception('Could not locate a data file for "%s"', $key);
        }

        $files = array_reverse($files);

        $data = array();
        foreach ($files as $file) {
            $data = Arr::merge($data, require $file);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $queuedData)
    {
        if (!$this->canSave || empty($queuedData)) {
            return false;
        }

        foreach ($queuedData as $name => $data) {
            $file = str_replace('<name>', $name, $this->saveFile);

            if ($this->canMergeData && is_file($file)) {
                $data = Arr::merge(require $file, $data);
            }

            file_put_contents($file, $this->exportCode($name, $data));
        }

        return true;
    }

    /**
     * Exports the data to PHP code.
     *
     * @param string $name
     * @param string $code
     *
     * @return string
     */
    public function exportCode($name, $code)
    {
        return strtr($this->getTemplate(), $this->getVars($name, $code));
    }

    /**
     * Returns the template for the generated data.
     *
     * @return string
     */
    public function getTemplate()
    {
        return file_get_contents($this->cfs->findFile('views', 'rax/data/driver/file-data-driver/data', 'tmpl'));
    }

    /**
     * Returns the template variables.
     *
     * @param string $name
     * @param string $code
     *
     * @return array
     */
    public function getVars($name, $code)
    {
        $files = array();
        foreach ($this->cfs->getFileExtensions() as $ext) {
            if ($this->saveFileExtension === $ext) {
                continue;
            }

            $files[] = sprintf(' * - %s/%s.%s', $this->scanDir, $name, $ext);
        }

        return array(
            '<code>'  => Php::varExport($code),
            '<files>' => implode("\n", $files),
            '<type>'  => $this->scanDir,
        );
    }
}
