<?php

/**
 * tonyenc.php: Encrypt or decrypt the script with tonyenc.
 *
 * A high performance and cross-platform encrypt extension for PHP source code.
 *
 * @author:  Tony
 * @site:    lihancong.cn
 */


if (version_compare(PHP_VERSION, 7, '<'))
    die("PHP must later than version 7.0\n");
if (php_sapi_name() !== 'cli')
    die("Must run in cli mode\n");
if (!extension_loaded('tonyenc'))
    die("The extension: 'tonyenc' not loaded\n");
if ($argc <= 1)
    echo <<<EOF
usage: php tonyenc.php file.php ...     encrypt the php file(s) or directory(s)
       php tonyenc.php -d file.php ...  decrypt the php file(s) or directory(s)\n
EOF;

$mode = 1;
if (isset($argv[1]) and $argv[1] === '-d') {
    array_shift($argv);
    $mode = 0;
}
array_shift($argv);
foreach ($argv as $fileName) {
    if (is_file($fileName)) {
        handle($fileName, $mode);
    } elseif (is_dir($fileName)) {
        $DirectoriesIt = new RecursiveDirectoryIterator($fileName, FilesystemIterator::SKIP_DOTS);
        $AllIt         = new RecursiveIteratorIterator($DirectoriesIt);
        $It            = new RegexIterator($AllIt, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($It as $v)
            handle($v[0], $mode);
    } else {
        echo "Unknowing file: '$fileName'\n";
    }
}


function handle($file, $mode)
{
    if ($fp = fopen($file, 'rb+') and $fileSize = filesize($file)) {
        $old = fread($fp, $fileSize);
        $data = $mode ? tonyenc_encode($old) : tonyenc_decode($old);
        if ($data !== false) {
            if (file_put_contents($file, '') !== false) {
                rewind($fp);
                fwrite($fp, $data);
            }
        }
        fclose($fp);
    }
}
