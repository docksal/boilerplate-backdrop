<?php
/**
 * @file
 * Hooks related to image styles and effects.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define information about image effects provided by a module.
 *
 * This hook enables modules to define image manipulation effects for use with
 * an image style.
 *
 * @return
 *   An array of image effects. This array is keyed on the machine-readable
 *   effect name. Each effect is defined as an associative array containing the
 *   following items:
 *   - "label": The human-readable name of the effect.
 *   - "effect callback": The function to call to perform this image effect.
 *   - "dimensions passthrough": (optional) Set this item if the effect doesn't
 *     change the dimensions of the image.
 *   - "dimensions callback": (optional) The function to call to transform
 *     dimensions for this effect.
 *   - "help": (optional) A brief description of the effect that will be shown
 *     when adding or configuring this image effect.
 *   - "form callback": (optional) The name of a function that will return a
 *     $form array providing a configuration form for this image effect.
 *   - "summary theme": (optional) The name of a theme function that will output
 *     a summary of this image effect's configuration.
 *
 * @see hook_image_effect_info_alter()
 */
function hook_image_effect_info() {
  $effects = array();

  $effects['my_module_resize'] = array(
    'label' => t('Resize'),
    'help' => t('Resize an image to an exact set of dimensions, ignoring aspect ratio.'),
    'effect callback' => 'my_module_resize_effect',
    'dimensions callback' => 'my_module_resize_dimensions',
    'form callback' => 'my_module_resize_form',
    'summary theme' => 'my_module_resize_summary',
  );

  return $effects;
}

/**
 * Alter the information provided in hook_image_effect_info().
 *
 * @param $effects
 *   The array of image effects, keyed on the machine-readable effect name.
 *
 * @see hook_image_effect_info()
 */
function hook_image_effect_info_alter(&$effects) {
  // Override the Image module's crop effect with more options.
  $effects['image_crop']['effect callback'] = 'my_module_crop_effect';
  $effects['image_crop']['dimensions callback'] = 'my_module_crop_dimensions';
  $effects['image_crop']['form callback'] = 'my_module_crop_form';
}

/**
 * Respond to image style updating.
 *
 * This hook enables modules to update settings that might be affected by
 * changes to an image. For example, updating a module specific variable to
 * reflect a change in the image style's name.
 *
 * @param $style
 *   The image style array that is being updated.
 */
function hook_image_style_save($style) {
  // If a module defines an image style and that style is renamed by the user
  // the module should update any references to that style.
  if (isset($style['old_name']) && $style['old_name'] == config_get('my_module.settings', 'image_style')) {
     config_set('my_module.settings', 'image_style', $style['name']);
  }
}

/**
 * Respond to image style deletion.
 *
 * This hook enables modules to update settings when a image style is being
 * deleted. If a style is deleted, a replacement name may be specified in
 * $style['name'] and the style being deleted will be specified in
 * $style['old_name'].
 *
 * @param $style
 *   The image style array that being deleted.
 */
function hook_image_style_delete($style) {
  // Administrators can choose an optional replacement style when deleting.
  // Update the modules style variable accordingly.
  if (isset($style['old_name']) && $style['old_name'] == config_get('my_module.settings', 'image_style')) {
    config_set('my_module.settings', 'image_style', $style['name']);
  }
}

/**
 * Respond to image style flushing.
 *
 * This hook enables modules to take effect when a style is being flushed (all
 * images are being deleted from the server and regenerated). Any
 * module-specific caches that contain information related to the style should
 * be cleared using this hook. This hook is called whenever a style is updated,
 * deleted, or any effect associated with the style is update or deleted.
 *
 * @param $style
 *   The image style array that is being flushed.
 */
function hook_image_style_flush($style) {
  // Empty cached data that contains information about the style.
  cache('my_module')->flush();
}

/**
 * Modify any image styles provided by other modules or the user.
 *
 * This hook allows modules to modify, add, or remove image styles. This may
 * be useful to modify default styles provided by other modules or enforce
 * that a specific effect is always enabled on a style. Note that modifications
 * to these styles may negatively affect the user experience, such as if an
 * effect is added to a style through this hook, the user may attempt to remove
 * the effect but it will be immediately be re-added.
 *
 * The best use of this hook is usually to modify default styles, which are not
 * editable by the user until they are overridden, so such interface
 * contradictions will not occur. This hook can target default (or user) styles
 * by checking the $style['storage'] property.
 *
 * If your module needs to provide a new style (rather than modify an existing
 * one) place the whole config file in your modules /config directory instead.
 */
function hook_image_styles_alter(&$styles) {
  // Check that we only affect a default style.
  if ($styles['thumbnail']['storage'] == IMAGE_STORAGE_DEFAULT) {
    // Add an additional effect to the thumbnail style.
    $styles['thumbnail']['effects'][] = array(
      'name' => 'image_desaturate',
      'data' => array(),
      'weight' => 1,
      'effect callback' => 'image_desaturate_effect',
    );
  }
}

/**
 * Modify the array of supported image extensions.
 *
 * @param array $supported_extensions
 *   Array of file extensions that are supposed to be supported.
 *
 * @see image_field_widget_form()
 */
function hook_image_supported_extensions_alter(&$supported_extensions) {
  // Add Wireless Bitmap Image File to the list.
  $supported_extensions[] = 'wbmp';
}

/**
 * @} End of "addtogroup hooks".
 */
