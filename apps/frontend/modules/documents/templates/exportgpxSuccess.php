<?php 
$points = $sf_data->getRaw('points');
$nbpts = count($points);
$id = $sf_params->get('id');
?>
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" creator="camptocamp.org" version="1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
  <metadata>
    <link href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/$id/" . $sf_params->get('lang') . "/$slug"; ?>">
      <text>Camptocamp.org</text>
    </link>
  </metadata>
<?php if ($nbpts > 1): ?>
  <trk>
    <name><?php echo $sf_data->getRaw('name') ?></name>
    <trkseg>
<?php 
$minlat = 90;  $minlon = 180;  $maxlat = -90; $maxlon = -180;
foreach ($points as $point): ?>
<?php $_point = explode(' ', trim($point)); 
$nb_fields = count($_point); 
$lon = number_format($_point[0], 6, '.', '');
$lat = number_format($_point[1], 6, '.', '');
$minlon = min($minlon, $lon);
$minlat = min($minlat, $lat);
$maxlon = max($maxlon, $lon);
$maxlat = max($maxlat, $lat);
?>
      <trkpt lat="<?php echo $lat ?>" lon="<?php echo $lon ?>"> 
<?php if ($nb_fields > 2): ?>
        <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
<?php if ($nb_fields == 4 && $_point[3]!=0): ?>
        <time><?php echo date('c', $_point[3]) ?></time>
<?php endif ?>
      </trkpt>
<?php endforeach ?>
    </trkseg>
  </trk>
  <?php //echo '<bounds minlat="'.$minlat.'" minlon="'.$minlon.'" maxlat="'.$maxlat.'" maxlon="'.$maxlon.'"/>'; ?>
<?php elseif ($nbpts = 1): ?>
<?php $_point = explode(' ', trim($points[0]));
        $lon = number_format($_point[0], 6, '.', '');
        $lat = number_format($_point[1], 6, '.', '');
?>
  <?php //echo '<bounds minlat="'.$lat.'" minlon="'.$lon.'" maxlat="'.$lat.'" maxlon="'.$lon.'"/>'; ?>
  <wpt lat="<?php echo $lat ?>" lon="<?php echo $lon ?>">
<?php if (count($_point) > 2): ?>
    <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
    <name><?php echo $sf_data->getRaw('name') ?></name>
  </wpt>
<?php endif ?>
</gpx>
