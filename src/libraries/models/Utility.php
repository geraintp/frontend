<?php
class Utility
{
  public static function dateLong($ts, $write = true)
  {
    return self::returnValue(date('l, F jS, Y \a\t g:ia', $ts), $write);
  }

  public static function permissionAsText($permission, $write = true)
  {
    self::returnValue(($permission ? 'public' : 'private'), $write);
  }

  public static function timeAsText($time, $prefix = null, $suffix = null, $write = true)
  {
    if(empty($time))
      return self::returnValue('', $write);

    $seconds = intval(time() - $time);
    $hours = intval($seconds / 3600);
    if($hours < 0)
      return self::returnValue('--', $write);
    elseif($hours < 1)
      return self::returnValue("{$prefix} a few minutes ago {$suffix}", $write);
    elseif($hours < 24)
      return self::returnValue("{$prefix} {$hours} " . self::plural($hours, 'hour', false) . " ago {$suffix}", $write);

    $days = intval($seconds / 86400);
    if($days <= 7)
      return self::returnValue("{$prefix} {$days} " . self::plural($days, 'day', false) . " ago {$suffix}", $write);

    $weeks = intval($days / 7);
    if($weeks <= 4)
      return self::returnValue("{$prefix} {$weeks} " . self::plural($weeks, 'week', false) . " ago {$suffix}", $write);

    $months = intval($days / 30);
    if($months < 12)
      return self::returnValue("{$prefix} {$months} " . self::plural($months, 'month', false) . " ago {$suffix}", $write);

    $years = intval($days / 365);
    return self::returnValue("{$prefix} {$years} " . self::plural($years, 'year', false) . " ago {$suffix}", $write);
  }

  public static function isActiveTab($label)
  {
    if(!isset($_GET['__route__']))
      return $label == 'home';

    $route = $_GET['__route__'];
    switch($label)
    {
      case 'home':
        if(preg_match('#^/$#', $route))
          return true;
        return false;
        break;
      case 'photo':
      case 'photos':
        if(!empty($route) && preg_match('#^/photo#', $route) && !preg_match('#^/photos/upload#', $route))
          return true;
        return false;
        break;
      case 'tags':
        if(!empty($route) && preg_match('#^/tags$#', $route))
          return true;
        return false;
        break;
      case 'upload':
        if(!empty($route) && preg_match('#^/photos/upload#', $route))
          return true;
        return false;
        break;
    }
  }

  public static function photoUrl($photo, $key)
  {
    return self::returnValue($photo[sprintf('path%s', $key)]);
  }

  public static function plural($int, $word = null, $write = true)
  {
    $word = self::safe($word, false);
    if(empty($word))
      return self::returnValue(($int > 1 ? 's' : ''), $write);
    else
      return self::returnValue(($int > 1 ? "{$word}s" : $word), $write);
  }

  public static function returnValue($value, $write = true)
  {
    if($write)
      echo $value;
    else
      return $value;
  }

  public static function safe($string, $write = true)
  {
    return self::returnValue(htmlspecialchars($string), $write);
  }

  public static function staticMapUrl($latitude, $longitude, $zoom, $size, $type = 'roadmap', $write = true)
  {
    //http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=14&size=512x512&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false
    return self::returnValue("http://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size={$size}&maptype={$type}&markers=color:gray%7C{$latitude},{$longitude}&sensor=false", $write);
  }

  public static function tagsAsLinks($tags, $write = true)
  {
    $ret = array();
    sort($tags);
    foreach($tags as $tag)
      $ret[] = '<a href="/photos/tags-'.self::safe($tag, false).'">'.self::safe($tag, false).'</a>';

    return self::returnValue(implode(', ', $ret), $write);
  }
}
