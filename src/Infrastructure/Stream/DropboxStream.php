<?php
namespace Infrastructure\Stream;

class DropboxStream
{
    private $done = false;
    private $test = '';
    private $file = '';
    private $oauth = '';

    /**
     * @var \Dropbox_API
     */
    private $dbox = null;

    private $full_path = '';
    private $count = 0;
    private $buffer = '';

    private $outputBuffer;
    private $outputBufferPosition = 0;

     /**
     * resource context
     *
     * @var resource
     */
    //public $context;

    /**
     * constructor
     *
     */
    public function __construct()
    {

    }

    /**
     *
     *
     * @return bool
     */
    public function dir_closedir()
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @param  int    $options
     * @return bool
     */
    public function dir_opendir($path , $options)
    {

    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function dir_readdir()
    {

    }

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function dir_rewinddir()
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @param  int    $mode
     * @param  int    $options
     * @return bool
     */
    public function mkdir($path , $mode , $options)
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $path_from
     * @param  string $path_to
     * @return bool
     */
    public function rename($path_from , $path_to)
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @param  int    $options
     * @return bool
     */
    public function rmdir($path , $options)
    {

    }

    /**
     * Enter description here...
     *
     * @param  int      $cast_as
     * @return resource
     */
    public function stream_cast($cast_as)
    {

    }

    /**
     * Enter description here...
     *
     */
    public function stream_close()
    {
        return true;
    }

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function stream_eof()
    {
        // we only need to do one read
        $this->done = true;

        return true;
    }

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function stream_flush()
    {
        if (!empty($this->buffer)) {
            if ($this->dbox->putFile($this->file, null, null, $this->buffer)) {
                $this->buffer = '';

                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Enter description here...
     *
     * @param  mode $operation
     * @return bool
     */
    public function stream_lock($operation)
    {

    }

    public function initDropbox($path)
    {
        $this->full_path = $path;
        $url_data = parse_url($path);

        if (!isset($url_data['user']) || !isset($url_data['pass']) || !isset($url_data['path']) || !isset($url_data['query'])) {
            return false;
        }

        parse_str($url_data['query'], $query);
        if (!$query) {
            return false;
        }

        $this->oauth = new \Dropbox_OAuth_PHP($query['key'], $query['secret']);
        $this->oauth->setToken(array('token'=>$url_data['user'], 'token_secret'=>$url_data['pass']));
        $this->dbox = new \Dropbox_API($this->oauth);
        $this->file = $url_data['path'];
    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @param  string $mode
     * @param  int    $options
     * @param  string &$opened_path
     * @return bool
     */
    public function stream_open($path , $mode , $options , &$opened_path)
    {
        $this->initDropbox($path);

        return true;
    }

    public function get_tokens()
    {
        return array('token'=>$this->oauth->oauth_token, 'token_secret'=>$this->oauth->oauth_token_secret);
    }

    /**
     * Enter description here...
     *
     * @param  int    $count
     * @return string
     */
    public function stream_read($count)
    {
        if (empty($this->outputBuffer)) {
            $this->outputBuffer = $this->dbox->getFile($this->file);
        }

        $results = substr($this->outputBuffer, $this->outputBufferPosition, $count);
        $this->outputBufferPosition += strlen($results);

        if ($this->outputBufferPosition >= strlen($this->outputBuffer)) {
            $this->outputBuffer = null;
        }

        return $results;
    }

    /**
     * Enter description here...
     *
     * @param  int  $offset
     * @param  int  $whence = SEEK_SET
     * @return bool
     */
    public function stream_seek($offset , $whence = SEEK_SET)
    {
        return true;
    }

    /**
     * Enter description here...
     *
     * @param  int  $option
     * @param  int  $arg1
     * @param  int  $arg2
     * @return bool
     */
    public function stream_set_option($option , $arg1 , $arg2)
    {

    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function stream_stat()
    {
        return stat( $this->full_path );
    }

    /**
     * Enter description here...
     *
     * @return int
     */
    public function stream_tell()
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $data
     * @return int
     */
    public function stream_write($data)
    {
        $test = strlen($data);
        $this->buffer .= $data;

        return $test;

    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @return bool
     */
    public function unlink($path)
    {

    }

    /**
     * Enter description here...
     *
     * @param  string $path
     * @param  int    $flags
     * @return array
     */
    public function url_stat($path , $flags)
    {
        $this->initDropbox($path);

        try {
            $results = $this->dbox->getMetaData($this->file);

            $e = array('size'=> $results['size'],'mtime'=> strtotime($results['modified']), 'atime' => time() );

            return $e;
        } catch (\Dropbox_Exception $e) {
            return false;
        }

    }
}
