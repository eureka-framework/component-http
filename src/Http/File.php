<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

/**
 * $_GET data wrapper class.
 *
 * @author Romain Cottard
 */
class File extends Data
{
    /**
     * @var Data $instance Current class instance.
     */
    protected static $instance = null;

    /**
     * @var string $name Data name to check.
     */
    protected $name = '';

    /**
     * File constructor.
     */
    protected function __construct()
    {
        $this->data = $_FILES;
    }

    /**
     * Use name as default in File's methods.
     *
     * @param  string $name
     * @return self
     * @throws \Exception
     */
    public function useName($name)
    {
        if (empty($name)) {
            throw new \Exception('Given name is empty!');
        }

        if (!isset($_FILES[$name])) {
            throw new \Exception('Given name not exists in $_FILES var!');
        }

        $this->data = $_FILES[$name];

        return $this;
    }

    /**
     * Check if upload is ok or not.
     *
     * @return bool
     */
    public function hasError()
    {
        return (UPLOAD_ERR_OK === $this->data['error']);
    }

    /**
     * Move uploaded file.
     *
     * @param  string $toDir
     * @param  string $fileName
     * @param  int    $mode Right mode on directory to create (if not exists)
     * @return bool
     * @throws \Exception
     */
    public function move($toDir, $fileName = null, $mode = 0775)
    {
        if ($this->hasError()) {
            throw new \Exception('Cannot move file, the file has not been correctly uploaded!');
        }

        $fileName = (empty($fileName) ? $this->get('name') : $fileName);

        if (!file_exists($toDir) && !mkdir($toDir, $mode, true)) {
            throw new \Exception(__METHOD__ . '|Cannot create directory "' . $toDir . '" !', 1000);
        }

        return move_uploaded_file($this->get('tmp_name'), $toDir . $fileName);
    }
}