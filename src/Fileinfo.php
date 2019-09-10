<?php

namespace OSW3;

class Fileinfo
{
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
    private $type;

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
    private $fread;

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
            $this
                // File Info
                ->setFilename()
                ->setExtension()
                ->setMimetype()
                ->setMimetypeExtension()
                ->setType()
                ->setSize()
                ->setDescription()
                ->setStat()

                // File content
                ->setHeader()
                ->setContent()
                // ->setFread()
                ->setMd5()
                ->setSha1()
                ->setBase64()
                
                // Image
                ->setThumbnail()
                ->setExif()
                ->setImageSizes()
                ->setOrientation()

                // Audio
                ->setId3Tags()
            ;
        }
    }

    public function __destruct()
    {
        if ($this->proceedOnTemp && file_exists($this->base)) 
        {
            unlink($this->base);
        }
    }

    public function info($name = null)
    {
        $info = array(
            "source" => $this->source,
            "base" => $this->base,

            // File info
            "filename"          => $this->filename,
            "extension"         => $this->extension,
            "mimetype"          => $this->mimetype,
            "mimetypeExtension" => $this->mimetypeExtension,
            "type"              => $this->type,
            "size"              => $this->size,
            "description"       => $this->description,
            // "stat"              => $this->stat,
        );

        return $info;
    }
    public function content($name = null)
    {
        $data = array(
            "header"    => $this->header,
            "content"   => $this->content,
            "fread"     => $this->fread,
            "base64"    => $this->base64,
            "data64"    => $this->data64,
            "md5"       => $this->md5,
            "sha1"      => $this->sha1,
        );

        return (isset($data[$name])) 
            ? $data[$name] 
            : $data;
    }
    public function header()
    {
        return $this->header;
    }
    public function image($name = null)
    {
        $data = array(
            "thumbnail"     => $this->thumbnail,
            "width"         => $this->width,
            "height"        => $this->height,
            "orientation"   => $this->orientation,
            "bits"          => $this->bits,
            "channels"      => $this->channels,
            "exif"          => $this->exif,
        );

        return (isset($data[$name])) 
            ? $data[$name] 
            : $data;
    }
    public function audio($name = null)
    {
        $data = array(
            "id3Tags"   => $this->id3Tags,
        );

        return (isset($data[$name])) 
            ? $data[$name] 
            : $data;
    }



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

    public function isValidFile()
    {
        $filetype = \filetype( $this->base );
        $isFile = is_file( $this->base );

        return $filetype === 'file' && $isFile;
    }

    public function getMimeDatabase($mimetype = null)
    {
        if (isset($this->mimeDatabase[$mimetype]))
        {
            return $this->mimeDatabase[$mimetype];
        }

        return $this->mimeDatabase;
    }


    /**
     * Base to proceed to parsing
     */
    public function getBase()
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
    public function getBasename()
    {
        return $this->basename;
    }
    public function setBasename(string $file)
    {
        $this->basename = \pathinfo($file, PATHINFO_BASENAME);

        return $this;
    }

    /**
     * Filename
     * 
     * The name of file before the extension
     */
    public function getFilename()
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
    public function getExtension()
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
    public function getMimetype()
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
    public function getMimetypeExtension()
    {
        return $this->mimetypeExtension;
    }
    public function setMimetypeExtension()
    {
        $this->mimetypeExtension = $this->getMimeDatabase( $this->mimetype );

        return $this;
    }

    /**
     * Media Type
     */
    public function getType()
    {
        return $this->type;
    }
    public function setType()
    {
        $type = explode("/", $this->mimetype);
        $this->type = $type[0];

        return $this;
    }

    /**
     * File Stat
     */
    public function getStat()
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
    public function getSize()
    {
        return $this->size;
    }
    public function setSize()
    {
        $this->size = \filesize( $this->base );

        return $this;
    }

    /**
     * Get file header
     */ 
    public function getHeader()
    {
        return $this->header;
    }
    public function setHeader()
    {
        $file = file($this->base);
        $header = $file[0];

        $header = explode("\n", $header);

        $this->header = $header[0];

        return $this;
    }

    /**
     * File content
     */
    public function getContent()
    {
        return $this->content;
    }
    public function setContent()
    {
        $this->content = \file_get_contents( $this->base );

        return $this;
    }

    /**
     * File content
     */
    public function getFread()
    {
        return $this->fread;
    }
    private function setFread()
    {
        $handle = fopen($this->base, 'r');
        $this->fread = fread($handle, filesize($this->base) );
        fclose($handle);

        return $this;
    }

    /**
     * Hash MD5 of the content
     */
    public function getMd5()
    {
        return $this->md5;
    }
    public function setMd5()
    {
        $this->md5 = \md5( $this->content );

        return $this;
    }

    /**
     * Hash SHA1 of the content
     */
    public function getSha1()
    {
        return $this->sha1;
    }
    public function setSha1()
    {
        $this->sha1 = \sha1( $this->content );

        return $this;
    }

    /**
     * Base62 of the content
     */
    public function getBase64()
    {
        return $this->base64;
    }
    public function getData64()
    {
        return $this->data64;
    }
    public function setBase64()
    {
        $this->base64 = base64_encode( $this->content );
        $this->data64 = "data:".$this->mimetype.";base64,".$this->base64;

        return $this;
    }

    /**
     * File description
     */
    public function getDescription()
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

        $this->description = \xtrim(",", $description);

        return $this;
    }

    /**
     * Image Thumbnail
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
    public function setThumbnail()
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
    public function getExif()
    {
        return $this->exif;
    }
    public function setExif()
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
    public function getImageSizes()
    {
        return $this->imageSizes;
    }
    public function setImageSizes()
    {
        if (\function_exists('getimagesize') && $this->type == "image")
        {
            $this->imageSizes = \getimagesize( $this->base );
    
            $this->width = $this->imageSizes[0];
            $this->height = $this->imageSizes[1];
            $this->bits = $this->imageSizes['bits'];
            $this->channels = $this->imageSizes['channels'];
        }

        return $this;
    }

    /**
     * Get the value of orientation
     */ 
    public function getOrientation()
    {
        return $this->orientation;
    }
    public function setOrientation()
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
    public function getId3Tags()
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