<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Value object representing a file uploaded through an HTTP request.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * @author Romain Cottard
 */
class UploadedFile
{
    /**
     * @var bool $isValid
     */
    private $isValid = false;

    /**
     * @var bool $isMoved
     */
    private $isMoved = false;

    /**
     * @var string $clientFilename
     */
    private $clientFilename = '';

    /**
     * @var string $clientMediaType
     */
    private $clientMediaType = '';

    /**
     * @var int $size
     */
    private $size = 0;

    /**
     * @var int $error
     */
    private $error = 0;

    /**
     * @var string $filename
     */
    private $filename = '';

    /**
     * UploadedFile constructor.
     *
     * @param string $file
     * @param string $name
     * @param int    $size
     * @param string $type mimetype
     * @param int    $errorCode
     */
    public function __construct($file, $name, $size, $name, $type, $errorCode)
    {
        $this->setFilename($file);
        $this->setSize($size);
        $this->setClientMediaType($type);
        $this->setClientFilename($name);
        $this->setError($errorCode);
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function getStream()
    {
        if ($this->isMoved()) {
            throw new \RuntimeException('File already moved. Cannot get resource stream for it.');
        }

        return new Stream(Stream::createResourceTemp());
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     */
    public function moveTo($targetPath)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    public function createAllFromGlobal()
    {
        $files = [];
        foreach ($_FILES as $index => $data) {
            foreach ($data as $name)
            {

            }
        }
    }

    /**
     * Get if file is already moved.
     *
     * @return bool
     */
    private function isMoved()
    {
        return $this->isMoved;
    }

    /**
     * Set client filename.
     *
     * @param  string $clientFilename
     * @return self
     * @throws \InvalidArgumentException
     */
    private function setClientFilename($clientFilename)
    {
        if (preg_match('`[/\\]+`', $clientFilename) > 0) {
            throw new \InvalidArgumentException('Filename contain invalid character "\" or "/" in his name.');
        }

        $this->clientFilename = (empty($clientFilename) ? null : (string) $clientFilename);

        return $this;
    }

    /**
     * Client media type (mime type)
     *
     * @param  string $clientMediaType
     * @return self
     */
    private function setClientMediaType($clientMediaType)
    {
        $this->clientMediaType = (empty($clientMediaType) ? null : (string) $clientMediaType);

        return $this;
    }

    /**
     * Set error code.
     *
     * @param  $error
     * @return self
     */
    private function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Set file size
     *
     * @param  int $size
     * @return self
     */
    private function setSize($size = null)
    {
        if ($size !== null) {
            $size = (int) $size;

            if ($size < 0) {
                throw new \RuntimeException('File size cannot be a negative value ! (size: ' . $size . ')');
            }
        }

        $this->size = $size;

        return $this;
    }

    /**
     * @param  string $filename Temp filename.
     * @return self
     */
    private function setFilename($filename)
    {
        if (!is_readable($filename)) {
            throw new \RuntimeException('Uploaded file is not readable !');
        }

        $this->filename = $filename;

        return $this;
    }
}
