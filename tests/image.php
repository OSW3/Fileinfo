<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OSW3\Fileinfo;

$file = new Fileinfo('./files/unicorn.jpg');
?>



<h2>Info</h2>

<dl>
    <dt><h3><code>Fileinfo::INFO_RELPATH</code></h3></dt>
    <dd>Relative Path</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_RELPATH) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_ABSPATH</code></h3></dt>
    <dd>Absoulte Path</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_ABSPATH) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_BASENAME</code></h3></dt>
    <dd>File base name</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_BASENAME) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_FILENAME</code></h3></dt>
    <dd>File name</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_FILENAME) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_EXTENSION</code></h3></dt>
    <dd>File extension</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_EXTENSION) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_MIMETYPE</code></h3></dt>
    <dd>Data mimetype</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_MIMETYPE) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_MIMETYPE_EXTENSION</code></h3></dt>
    <dd>Recommended from mimetype</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_MIMETYPE_EXTENSION) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_FILETYPE</code></h3></dt>
    <dd>File / Media type</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_FILETYPE) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_SIZE</code></h3></dt>
    <dd>File size</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_SIZE) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::INFO_DESCRIPTION</code></h3></dt>
    <dd>File description</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::INFO_DESCRIPTION) ) ?></pre></dd>

</dl>



<h2>Content</h2>

<dl>
    <dt><h3><code>Fileinfo::CONTENT_HEADER</code></h3></dt>
    <dd>File Header</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::CONTENT_HEADER) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_BASE64</code></h3></dt>
    <dd>BASE64 of content</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::CONTENT_BASE64) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_DATA64</code></h3></dt>
    <dd>BASE64 of content with prefix (ready to use in img src attribut)</dd>
    <dd>
        <pre><?php print_r( $file->get(Fileinfo::CONTENT_DATA64) ) ?></pre>
        <img src="<?= $file->get(Fileinfo::CONTENT_DATA64) ?>">
    </dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_MD5</code></h3></dt>
    <dd>MD5 of content</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::CONTENT_MD5) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_SHA1</code></h3></dt>
    <dd>SHA1 of content</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::CONTENT_SHA1) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_ROWS</code></h3></dt>
    <dd>Rows of content</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::CONTENT_ROWS) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::CONTENT_DATA</code></h3></dt>
    <dd>File content</dd>
    <dd><pre><?php /** print_r( $file->get(Fileinfo::CONTENT_DATA) ) /**/ ?></pre></dd>

</dl>



<h2>Image</h2>

<dl>
    <dt><h3><code>Fileinfo::IMAGE_THUMBNAIL</code></h3></dt>
    <dd>Image Thumbnail (if is available)</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_THUMBNAIL) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_WIDTH</code></h3></dt>
    <dd>Image Width</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_WIDTH) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_HEIGHT</code></h3></dt>
    <dd>Image Height</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_HEIGHT) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_ORIENTATION</code></h3></dt>
    <dd>Image orientation</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_ORIENTATION) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_BITS</code></h3></dt>
    <dd>Bits</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_BITS) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_CHANNELS</code></h3></dt>
    <dd>Channels</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_CHANNELS) ) ?></pre></dd>
    <hr>

    <dt><h3><code>Fileinfo::IMAGE_EXIF</code></h3></dt>
    <dd>Image EXIF</dd>
    <dd><pre><?php print_r( $file->get(Fileinfo::IMAGE_EXIF) ) ?></pre></dd>

</dl>