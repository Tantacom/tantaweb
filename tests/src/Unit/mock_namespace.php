<?php

namespace Drupal\social_media_links {

  use Drupal\Core\Site\Settings;

  /**
   * Mock function.
   *
   * @return bool
   *   TRUE
   */
  function drupal_get_profile() {
    global $install_state;

    if (drupal_installation_attempted()) {
      // If the profile has been selected return it.
      if (isset($install_state['parameters']['profile'])) {
        $profile = $install_state['parameters']['profile'];
      }
      else {
        $profile = NULL;
      }
    }
    else {
      // Fall back to NULL, if there is no 'install_profile' setting.
      $profile = Settings::get('install_profile');
    }

    return $profile;
  }

  /**
   * Mock function.
   *
   * @return bool
   *   TRUE
   */
  function drupal_get_path() {
    return TRUE;
  }

  /**
   * Returns TRUE if a Drupal installation is currently being attempted.
   */
  function drupal_installation_attempted() {
    return FALSE;
  }
}
