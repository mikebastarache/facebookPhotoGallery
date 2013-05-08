<?php

/**
 * This class provides static methods that return pieces of data specific to
 * your app
 */
class AppInfo {

  /*****************************************************************************
   *
   * These functions provide the unique identifiers that your app users.  These
   * have been pre-populated for you, but you may need to change them at some
   * point.  They are currently being stored in 'Environment Variables'.  To
   * learn more about these, visit
   *   'http://php.net/manual/en/function.getenv.php'
   *
   ****************************************************************************/

  /**
   * @return the appID for this app
   */
  public static function appID() {
    //return getenv('FACEBOOK_APP_ID');
    return "511434182252079";
  }

  /**
   * @return the appSecret for this app
   */
  public static function appSecret() {
    //return getenv('FACEBOOK_SECRET');
    return "9029a93bc73e40e8ef5f39d161c31cd3";
  }

/**
   * @return the ID for the amdminstrative account on Facebook
   */
  public static function adminID() {
    return "521680973";
  }

  
  /**
   * @return the fanpage
   */
  public static function fanpage() {
    return "183866985000238";
  }

  /**
   * @return the fanpage
   */
  public static function fanpageName() {
    return "MM - Development Platform";
  }
  
  /**
   * @return the albumID
   * this is the current album that all upload photos will get posted to
   */
  public static function albumID() {
    return "472992669421000";
  }

  /**
   * @return the albumID
   * this is used for the tile grid.  can list multiple albums by comma delimitted
   */
  public static function albumList() {
    return "472992669421000";
  }

  /**
   * @return the postFanpage
   * 0 = false, 1 = true
   */
  public static function postFanpage() {
    return 0;
  }
  
  /**
   * @return the postUserFeed
   * 0 = false, 1 = true
   */
  public static function postUserFeed() {
    return 0;
  }

  /**
   * @return the url
   */
  public static function getUrl($path = '/') {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
      || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    ) {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }

    return $protocol . $_SERVER['HTTP_HOST'] . $path;
  }


  /**
   * @return the fanpageUrl
   */
  public static function fanpageUrl() {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
      || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    ) {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }

    return $protocol . "www.facebook.com/mmdevel/app_511434182252079";
  }


  public static function redirectUrl() {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
      || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
    ) {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }
    //return $protocol . $_SERVER['HTTP_HOST'] . "/facebook/royale_fb_photos_2012";
    return $protocol . "localhost:48272";
  }


}
