<?php
/**
 * RackSpace CloudFiles implementation for FileSystemInterface
 *
 * This class defines the functionality defined by FileSystemInterface for CloudFiles.
 * @author Geraint Palmer <geraintp@gmail.com>
 */
class FileSystemCloudFiles implements FileSystemInterface
{
	/**
	  * Member variables holding the names to the bucket and the file system object itself.
	  * @access private
	  * @var array
	  */
	private $container, $conn, $auth;

	/**
	  * Constructor
	  *
	  * @return void
	  */
	public function __construct()
	{
		$cf = getConfig()->get('cloudfiles');
		
		//Authenticate		
		$this->auth = new CF_Authentication( $cf->cfUserName, $cf->cfApiKey,
											 null,
											 $cf->cfAuthUrl );
		
		try {
			if (!$this->auth->authenticate())
				throw new Exception("Access Denied, CloudFiles authentication failed.", 401 );
		} catch (Exception $e) {
			print_r($e);
		}

		$this->conn = new CF_Connection( $this->auth );
		$this->container = $this->conn->get_container( $cf->cfContainer );
	}
	
	/**
     * Deletes a photo (and all generated versions) from the file system.
     * To get a list of all the files to delete we first have to query the database and find out what versions exist.
     *
     * @param string $id ID of the photo to delete
     * @return boolean
     */
	public function deletePhoto( $id )
	{
		$isOK  = false;
		$photo = getDb()->getPhoto($id);
		
		foreach ($photo as $key => $value) {
			if( strncmp($key, 'path', 4) === 0 )
				$isOK = $this->container->delete_object( $this->normalizePath($value) );
			
			if (!$isOK )
				return $isOK;
		}
		return $isOK;
	}
	
  /**
    * Retrieves a photo from the remote file system as specified by $filename.
    * This file is stored locally and the path to the local file is returned.
    *
    * @param string $filename File name on the remote file system.
    * @return mixed String on success, FALSE on failure.
    */	
	public function getPhoto( $filename )
	{
		try {
			$object = $this->container->get_object( $this->normalizePath($filename) );
			$tmpname = dirname(dirname(dirname(__FILE__))).'/tmp/'.uniqid( 'opme', true );
			$object->save_to_filename( $tmpname );
		} 
		catch (Exception $e)
		{
			return false;
		}

		
		return  $tmpname;
	}
	
	/**
    * Writes/uploads a new photo to the remote file system.
    *
    * @param string $localFile File name on the local file system.
    * @param string $remoteFile File name to be saved on the remote file system.
    * 
    * @return boolean
    */
	public function putPhoto( $localFile, $remoteFile )
	{
		// upload file to Rackspace
		$object = $this->container->create_object( $this->normalizePath($remoteFile) );
		
		$fp = @fopen($localFile, "r");
        if (!$fp) {
            throw new IOException("Could not open file for reading: ".$localFile);
        }

        clearstatcache();

        $size = (float) sprintf("%u", filesize($localFile));
        if ($size > MAX_OBJECT_SIZE) {
            throw new SyntaxException("File size exceeds maximum object size.");
        }

        //$object->_guess_content_type($localFile);
		$object->content_type = "image/jpeg"; //mime_content_type($localFile);
		
		try 
		{
			$object->write( $fp, $size, true );
		} 
		catch (Exception $e) 
		{
			fclose($fp);
	        return false;
		}
        fclose($fp);
        return true;
	}
	
	public function putPhotos( $files )
	{
		foreach ( $files as $file ) {
			list($localFile, $remoteFile) = each($file);
			$this->putPhoto( $localFile, $remoteFile );
		}
		return true;
	}
	
	/**
    * Get the hostname for the remote filesystem to be used in constructing public URLs.
	*
	* @param boolean $ssl returns the ssl url for the container
    * @return string
    */
	public function getHost( $ssl=false )
	{
		if ($this->container->cdn_enabled) {
            return !$ssl ? $path = substr( $this->container->cdn_uri, 7) :substr( $this->container->cdn_ssl_uri, 8);
        }
        return NULL;	
	}
	
	/**
	* Removes leading slashes since it's can't have files names starting a slash, will cause error in upload.
	*
	* @param string $path Path of the photo to have leading slashes removed.
	* @return string
	*/
	private function normalizePath($path)
	{
		return preg_replace('/^\/+/', '', $path);
	}
	
	public function initialize()
	{
		//TODO
		// check container exists / 
			//create container if not there 
		
		//and make public.
		
		return true;
	}
}
?>