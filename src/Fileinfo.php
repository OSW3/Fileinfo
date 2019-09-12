<?php

namespace OSW3;

class Fileinfo
{
    const INFO_RELPATH              = 'relpath';
    const INFO_ABSPATH              = 'abspath';
    const INFO_BASENAME             = 'basename';
    const INFO_FILENAME             = 'filename';
    const INFO_EXTENSION            = 'extension';
    const INFO_MIMETYPE             = "mimetype";
    const INFO_MIMETYPE_EXTENSION   = "mimetypeExtension";
    const INFO_FILETYPE             = "filetype";
    const INFO_TYPE                 = "type";
    const INFO_SIZE                 = "size";
    const INFO_DESCRIPTION          = "description";

    const CONTENT_HEADER            = "header";
    const CONTENT_DATA              = "content";
    const CONTENT_BASE64            = "base64";
    const CONTENT_DATA64            = "data64";
    const CONTENT_MD5               = "md5";
    const CONTENT_SHA1              = "sha1";
    const CONTENT_ROWS              = "rows";

    const IMAGE_THUMBNAIL           = "thumbnail";
    const IMAGE_WIDTH               = "width";
    const IMAGE_HEIGHT              = "height";
    const IMAGE_ORIENTATION         = "orientation";
    const IMAGE_BITS                = "bits";
    const IMAGE_CHANNELS            = "channels";
    const IMAGE_EXIF                = "exif";

    const AUDIO_ID3TAGS             = "id3Tags";

    const DATABASE_FILE = '/../resources/database.php';

    const TEMP_DIR = './temp/';

    const EXIF_IMAGETYPE = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, 
                            IMAGETYPE_SWF, IMAGETYPE_PSD, IMAGETYPE_BMP, 
                            IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM, IMAGETYPE_JPC, 
                            IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_JB2, 
                            IMAGETYPE_SWC, IMAGETYPE_IFF, IMAGETYPE_WBMP, 
                            IMAGETYPE_XBM, IMAGETYPE_ICO, IMAGETYPE_WEBP];

    private $mimeDatabase;
    private $proceedOnTemp = false;

    /** 
     * The input source file
     * 
     * @var string 
     */
    private $source;

    /**
     * The source used to proceed the parsing
     *
     * @var string
     */
    private $base;
    private $basename;
    
    /**
     * The filename before the extension
     *
     * @var string
     */
    private $filename;

    /**
     * The file extension
     *
     * @var string
     */
    private $extension;

    /**
     * MIME Type
     *
     * @var string
     */
    private $mimetype;
    private $mimetypeExtension;

    /**
     * Media Type
     *
     * @var string
     */
    private $filetype;
    // TODO: private $type;
    // TODO: private $file;
    // TODO: private $dir;
    // TODO: private $link;

    /**
     * File size
     *
     * @var int
     */
    private $size;

    /**
     * File header
     *
     * @var string
     */
    private $header;

    /**
     * File data content
     *
     * @var string
     */
    private $content;

    /**
     * Rows of content
     *
     * @var int
     */
    private $rows;

    /**
     * Hash MD5 of the content
     * 
     * @var string
     */
    private $md5;

    /**
     * Hash SHA1 of the content
     * 
     * @var string
     */
    private $sha1;

    /**
     * Base62 of the content
     * 
     * @var string
     */
    private $base64;

    /**
     * Base64 prefixed by MimeType
     * 
     * @var string
     */
    private $data64;

    /**
     * File description
     * 
     * @var string
     */
    private $description;

    /**
     * Image Thumbnail
     *
     * @var string|false
     */
    private $thumbnail;
    
    /**
     * Image EXIF
     *
     * @var array
     */
    private $exif;

    /**
     * Image Sizes
     *
     * @var array
     */
    private $imageSizes;

    /**
     * Image Sizes
     *
     * @var int
     */
    private $width;
    private $height;
    private $bits;
    private $channels;
    private $orientation;

    private $id3Tags;
    
    
    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =


    public function __construct(string $source)
    {
        // Import MIME Type database
        $this->mimeDatabase = require __DIR__.self::DATABASE_FILE;

        // Set the source file
        $this->source = $source;

        // If source file don't exist, make it local temporary
        file_exists($this->source) 
            ? $this->setBase($this->source) 
            : $this->copyToLocalTemp();

        if ($this->isValidFile())
        {
            // File Info
            $this->setFilename();
            $this->setExtension();
            $this->setMimetype();
            $this->setMimetypeExtension();
            $this->setFiletype();
            $this->setSize();
            $this->setDescription();
            $this->setStat();
            $this->setType();

            // File content;
            $this->setHeader();
            $this->setContent();
            $this->setMd5();
            $this->setSha1();
            $this->setBase64();
            
            switch ($this->type)
            {
                case 'image':
                $this->setThumbnail();
                $this->setExif();
                $this->setImageSizes();
                $this->setOrientation();
                break;

                case 'audio':
                $this->setId3Tags();
                break;
            }
        }
    }

    public function __destruct()
    {
        if ($this->proceedOnTemp && file_exists($this->base)) 
        {
            unlink($this->base);
        }
    }


    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

    public function get($options = null)
    {
        $info = $this->info();
        if (isset($info[$options])) return $info[$options];

        $content = $this->content();
        if (isset($content[$options])) return $content[$options];

        $image = $this->image();
        if (isset($image[$options])) return $image[$options];

        return null;
    }

    public function info($options = null)
    {
        $data = array(
            self::INFO_RELPATH          => $this->source,
            self::INFO_ABSPATH          => $this->base,
            self::INFO_BASENAME         => $this->basename,
            self::INFO_FILENAME         => $this->filename,
            self::INFO_EXTENSION        => $this->extension,
            self::INFO_MIMETYPE         => $this->mimetype,
            self::INFO_MIMETYPE_EXTENSION => $this->mimetypeExtension,
            self::INFO_FILETYPE         => $this->filetype,
            self::INFO_TYPE             => $this->type,
            self::INFO_SIZE             => $this->size,
            self::INFO_DESCRIPTION      => $this->description,
            // "stat"              => $this->stat,
        );

        return (isset($data[$options])) 
            ? $data[$options] 
            : $data;
    }

    public function content($options = null)
    {
        $data = array(
            self::CONTENT_HEADER        => $this->header,
            self::CONTENT_DATA          => $this->content,
            self::CONTENT_BASE64        => $this->base64,
            self::CONTENT_DATA64        => $this->data64,
            self::CONTENT_MD5           => $this->md5,
            self::CONTENT_SHA1          => $this->sha1,
            self::CONTENT_ROWS          => $this->rows,
        );

        return (isset($data[$options])) 
            ? $data[$options] 
            : $data;
    }

    public function image($options = null)
    {
        $data = array(
            self::IMAGE_THUMBNAIL       => $this->thumbnail,
            self::IMAGE_WIDTH           => $this->width,
            self::IMAGE_HEIGHT          => $this->height,
            self::IMAGE_ORIENTATION     => $this->orientation,
            self::IMAGE_BITS            => $this->bits,
            self::IMAGE_CHANNELS        => $this->channels,
            self::IMAGE_EXIF            => $this->exif,
        );

        return (isset($data[$options])) 
            ? $data[$options] 
            : $data;
    }

    public function audio($options = null)
    {
        $data = array(
            "id3Tags"   => $this->id3Tags,
        );

        return (isset($data[$options])) 
            ? $data[$options] 
            : $data;
    }

    
    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =


    private function copyToLocalTemp()
    {
        // Retrieve the basename of the source
        // $basename = \pathinfo($this->source, PATHINFO_BASENAME);
        $this->setBasename( $this->source );

        // Define the temporary destination
        $destination = self::TEMP_DIR.$this->basename;

        // Create temporary directory if don't exist
        if (!\file_exists(self::TEMP_DIR) || !\is_dir(self::TEMP_DIR))
        {
            \mkdir(self::TEMP_DIR);
        }

        // Copy the source to the temporary directory
        copy($this->source, $destination);
        
        // Set $destination has base
        $this->setBase( $destination );

        $this->proceedOnTemp = true;

        return $this;
    }

    private function isValidFile()
    {
        $filetype = \filetype( $this->base );
        $isFile = is_file( $this->base );

        return $filetype === 'file' && $isFile;
    }

    private function getMimeDatabase($mimetype = null)
    {
        if (isset($this->mimeDatabase[$mimetype]))
        {
            return $this->mimeDatabase[$mimetype];
        }

        return $this->mimeDatabase;
    }

    public function boundary()
    {
        $x = "==========";

        return $x.\md5(\uniqid()).$x;
    }

    
    // = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =


    /**
     * Base to proceed to parsing
     */
    private function getBase()
    {
        return $this->base;
    }
    private function setBase($base)
    {
        $this->base = \realpath($base);
        $this->setBasename( $this->base );

        return $this;
    }

    /**
     * Get the value of basename
     */ 
    private function getBasename()
    {
        return $this->basename;
    }
    private function setBasename(string $file)
    {
        $this->basename = \pathinfo($file, PATHINFO_BASENAME);

        return $this;
    }

    /**
     * Filename
     * 
     * The name of file before the extension
     */
    private function getFilename()
    {
        return $this->filename;
    }
    private function setFilename()
    {
        $this->filename = \pathinfo($this->base, PATHINFO_FILENAME);

        return $this;
    }

    /**
     * Extension
     * 
     * The file extension
     */
    private function getExtension()
    {
        return $this->extension;
    }
    private function setExtension()
    {
        $this->extension = \pathinfo($this->base, PATHINFO_EXTENSION);

        return $this;
    }

    /**
     * File content MimeType
     */
    private function getMimetype()
    {
        return $this->mimetype;
    }
    private function setMimetype()
    {
        $this->mimetype = \mime_content_type( $this->base );

        return $this;
    }

    /**
     * Extension provide by MimeType
     */ 
    private function getMimetypeExtension()
    {
        return $this->mimetypeExtension;
    }
    private function setMimetypeExtension()
    {
        $this->mimetypeExtension = $this->getMimeDatabase( $this->mimetype );

        return $this;
    }

    /**
     * Media Filetype
     */
    private function getFiletype()
    {
        return $this->filetype;
    }
    private function setFiletype()
    {
        $filetype = explode("/", $this->mimetype);
        $this->filetype = $filetype[0];

        return $this;
    }

    /**
     * Get the value of type
     */ 
    // public function getType()
    // {
    //     return $this->type;
    // }
    // public function setType()
    // {
    //     $this->type = $type;

    //     return $this;
    // }

    /**
     * File Stat
     */
    private function getStat()
    {
        return $this->stat;
    }
    private function setStat()
    {
        // 0	dev	volume
        // 1	ino	Numéro d'inode (*)
        // 2	mode	droit d'accès à l'inode
        // 3	nlink	nombre de liens
        // 4	uid	userid du propriétaire (*)
        // 5	gid	groupid du propriétaire (*)
        // 6	rdev	type du volume, si le volume est une inode
        // 8	atime	date de dernier accès (Unix timestamp)
        // 9	mtime	date de dernière modification (Unix timestamp)
        // 10	ctime	date de dernier changement d'inode (Unix timestamp)
        // 11	blksize	taille de bloc (**)
        // 12	blocks	nombre de blocs de 512 octets alloués (**) 
        $this->stat = \stat( $this->base );

        return $this;
    }

    /**
     * File size
     */ 
    private function getSize()
    {
        return $this->size;
    }
    private function setSize()
    {
        $this->size = \filesize( $this->base );

        return $this;
    }

    /**
     * Get file header
     */ 
    private function getHeader()
    {
        return $this->header;
    }
    private function setHeader()
    {
        $boundary = $this->boundary();

        $file = file($this->base);

        $header = $file[0];
        $header = explode("\n", $header);
        $header = preg_replace('/[\x00-\x1F\x7F-\xFF]/', $boundary, $header[0]);
        $header = explode($boundary, $header);

        foreach ($header as $key => $value) 
        {
            if (empty(trim($value)))
            {
                unset($header[$key]);
            }
            else 
            {
                $header[$key] = trim($value);
            }
        }

        $header = implode(' ', $header);

        $this->header = $header;

        $this->setRows( count($file) );

        return $this;
    }

    /**
     * File content
     */
    private function getContent()
    {
        return $this->content;
    }
    private function setContent()
    {

        // $handle = fopen($this->base, 'r');
        // $this->content = fread($handle, filesize($this->base) );
        // fclose($handle);

        $this->content = \file_get_contents( $this->base );

        return $this;
    }

    /**
     * Rows of content
     */ 
    public function getRows()
    {
        return $this->rows;
    }
    public function setRows(int $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Hash MD5 of the content
     */
    private function getMd5()
    {
        return $this->md5;
    }
    private function setMd5()
    {
        $this->md5 = \md5( $this->content );

        return $this;
    }

    /**
     * Hash SHA1 of the content
     */
    private function getSha1()
    {
        return $this->sha1;
    }
    private function setSha1()
    {
        $this->sha1 = \sha1( $this->content );

        return $this;
    }

    /**
     * Base62 of the content
     */
    private function getBase64()
    {
        return $this->base64;
    }
    private function getData64()
    {
        return $this->data64;
    }
    private function setBase64()
    {
        $this->base64 = base64_encode( $this->content );
        $this->data64 = "data:".$this->mimetype.";base64,".$this->base64;

        return $this;
    }

    /**
     * File description
     */
    private function getDescription()
    {
        return $this->description;
    }
    private function setDescription()
    {
        if (\function_exists('finfo_open') && \function_exists('finfo_file') && \function_exists('finfo_close'))
        {
            $finfo = finfo_open();
            $description = \finfo_file($finfo, $this->base);
            \finfo_close($finfo);
        }

        $this->description = $description;

        return $this;
    }

    /**
     * Image Thumbnail
     */
    private function getThumbnail()
    {
        return $this->thumbnail;
    }
    private function setThumbnail()
    {
        if ($this->type == "image")
        {
            if (($thumbnail = @\exif_thumbnail( $this->base )) !== false)
            {
                $this->thumbnail = $thumbnail;
            }
        }

        return $this;
    }

    /**
     * Image EXIF
     */
    private function getExif()
    {
        return $this->exif;
    }
    private function setExif()
    {
        // foreach (self::EXIF_IMAGETYPE as $imagetype) 
        // {
        //     $exif_mimetype = image_type_to_mime_type( $imagetype );
        //     if ($exif_mimetype === $this->mimetype)
        //     {
        //         $this->exif = \exif_read_data( $this->base );
        //         continue;
        //     }
        // }

        // if ($this->mimetype === "image/jpeg")
        // {
        //     $this->exif = \exif_read_data( $this->base );
        // }

        if ($this->type == "image")
        {
            if (($exif = @\exif_read_data( $this->base )) !== false)
            {
                $this->exif = $exif;
            }
        }

        return $this;
    }

    /**
     * Image Sizes
     */ 
    private function getImageSizes()
    {
        return $this->imageSizes;
    }
    private function setImageSizes()
    {
        if (\function_exists('getimagesize') && $this->type == "image")
        {
            $this->imageSizes = \getimagesize( $this->base );
    
            $this->width    = isset($this->imageSizes[0])           ? $this->imageSizes[0] : null;
            $this->height   = isset($this->imageSizes[1])           ? $this->imageSizes[1] : null;
            $this->bits     = isset($this->imageSizes['bits'])      ? $this->imageSizes['bits'] : null;
            $this->channels = isset($this->imageSizes['channels'])  ? $this->imageSizes['channels'] : null;
        }

        return $this;
    }

    /**
     * Get the value of orientation
     */ 
    private function getOrientation()
    {
        return $this->orientation;
    }
    private function setOrientation()
    {
        if ($this->width > $this->height)
        {
            $orientation = "landscape";
        }
        else if ($this->width < $this->height)
        {
            $orientation = "protrait";
        }
        else
        {
            $orientation = "square";
        }

        $this->orientation = $orientation;

        return $this;
    }

    /**
     * ID3 Tags
     */
    private function getId3Tags()
    {
        return $this->id3Tags;
    }
    private function setId3Tags()
    {
        if (\function_exists('id3_get_tag') && $this->type == "audio")
        {
            $this->id3Tags = \id3_get_tag( $this->base );
        }

        return $this;
    }
}