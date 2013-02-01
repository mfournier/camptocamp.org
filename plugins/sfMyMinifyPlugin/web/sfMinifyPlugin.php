<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__).'/../../..'));
define('SF_APP', 'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG', false);
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$webdir = sfConfig::get('sf_web_dir');

/**
 * The Files controller only "knows" HTML, CSS, and JS files. Other files
 * would only be trim()ed and sent as plain/text.
 */
$serveExtensions = array('css', 'js');

// is debug mode set?
if (isset($_GET['no'])) {
  $debug = true;
}
else
{
  $debug = false;
}

// serve
if (isset($_GET['f']))
{
  $filenamePattern = '/(' . implode('|', $serveExtensions).   ')$/';
  if(preg_match($filenamePattern, $_GET['f'], $matches))
  {
    $files = split(',', $_GET['f']);
    $error = false;

    foreach($files as $key => $file)
    {
      if (!file_exists($webdir . $file))
      {
        $error = true;
      }
      else
      {
        $files[$key] = $webdir . $file;
      }
    }

    if(!$error)
    {
      set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'min'.DIRECTORY_SEPARATOR.'lib');
      require 'Minify.php';

      // check for URI versioning
      if (preg_match('/&\\d/', $_SERVER['QUERY_STRING'])) {
        $maxAge = 31536000;
      }
      else
      {
        $maxAge = 86400;
      }

      // debug = we don't minify. But we don't add /* line numbers */
      $options = array('files' => $files, 'maxAge' => $maxAge, 'debug' => false);

      if ($debug)
      {
        $options['minifiers'] = array(Minify::TYPE_JS => '', Minify::TYPE_CSS => '');
      }

      if (sfConfig::get('sf_cache'))
      {
        $minifyCachePath = sfConfig::get('sf_config_cache_dir') . DIRECTORY_SEPARATOR . 'minify';
        if(!is_dir($minifyCachePath))
        {
          mkdir($minifyCachePath);
        }
        Minify::setCache($minifyCachePath);
      }
      Minify::serve('Files', $options);
      exit();
    }
  }
}
$s = $_SERVER["SERVER_PROTOCOL"]." 404 Not Found";
header($s);
echo $s;
