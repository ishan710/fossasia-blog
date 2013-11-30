<?php
// $Id: url_alter.api.php,v 1.6 2009/10/31 01:42:15 davereid Exp $

/**
 * @file
 * Documentation for url_alter API.
 *
 * You can even write your code so that url_alter is used if it is active,
 * otherwise fallback to the custom_url_rewrite equivalents. For example put
 * the following code in your module's .module file. Adjust appropriately for
 * custom_url_rewrite_inbound().
 *
 * @code
 * // Define the custom_url_rewrite_outbound() function if not already defined.
 * if (!function_exists('custom_url_rewrite_outbound')) {
 *   function custom_url_rewrite_outbound(&$path, &$options, $original_path) {
 *     mymodule_url_outbound_alter($path, $options, $original_path);
 *   }
 * }
 *
 * // Implementation of hook_url_outbound_alter().
 * function mymodule_url_outbound_alter(&$path, &$options, $original_path) {
 *   // Perform alterations here.
 * }
 * @endcode
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Hook implementation of custom_url_rewrite_inbound().
 *
 * Alter incoming requests so they map to a Drupal path.
 *
 * This function can change the value of $result since it is passed by
 * reference.
 *
 * Please note that this function is called before modules are loaded and the
 * menu system is initialized. After execution it changes the value of
 * $_GET['q'].
 *
 * If you want to implement this hook, your module should also make
 * sure to implement hook_boot() so that your module is loaded at the time it
 * will be invoked.
 *
 * @param $result
 *   The Drupal path based on the database. If there is no match in the
 *   database it will be the same as $path.
 * @param $path
 *   The path to be rewritten.
 * @param $path_language
 *   An optional language code for the request.
 */
function hook_url_inbound_alter(&$result, $path, $path_language) {
  global $user;

  // Change all requests for 'article/x' to 'node/x'.
  if (preg_match('|^article(/.*)|', $path, $matches)) {
    $path = 'node'. $matches[1];
  }
  // Change all requests to 'e' to the user's profile edit page.
  if ($path == 'e') {
    $path = 'user/'. $user->uid .'/edit';
  }
}

/**
 * Hook implementation of custom_url_rewrite_outbound().
 *
 * Alter links generated by Drupal.
 *
 * This function can change the value of $path and $options since they are
 * passed by reference.
 *
 * This function is called from url(). Please note that this function is called
 * very frequently so performance is critical.
 *
 * To change a link from an internal link to an external link, you would set
 * $options['base_url'] to the base URL of the link and also set
 * $options['absolute'] to TRUE. This will only work if clean URLs are enabled.
 *
 * @param $path
 *   The alias of the $original_path as defined in the database. If there is no
 *   such match in the database it will be the same as $original_path.
 * @param $options
 *   An associative array of additional options that were passed to url().
 * @param $original_path
 *   The unaliased Drupal path that is being linked.
 */
function hook_url_outbound_alter(&$path, &$options, $original_path) {
  global $user;

  // Change all links for 'node/x' to 'article/x'.
  if (preg_match('|^node(/.*)|', $path, $matches)) {
    $path = 'article'. $matches[1];
  }
  // Change all links to the user's profile edit page to a path 'e'.
  if ($path == 'user/'. $user->uid .'/edit') {
    $path = 'e';
  }
}

/**
 * @} End of "addtogroup hooks".
 */
