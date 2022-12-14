<?php

namespace Drupal\date_augmenter\Plugin;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Describes a configurable Date Augmenter plugin.
 *
 * Plugins which implement this interface which also want to add a plugin form
 * should also implement \Drupal\Core\Plugin\PluginFormInterface.
 *
 * @see \Drupal\Core\Plugin\PluginFormInterface
 * @see \Drupal\date_augmenter\Plugin\PluginFormTrait
 */
interface ConfigurablePluginInterface extends PluginInspectionInterface, DerivativeInspectionInterface, ConfigurableInterface, DependentPluginInterface, ContainerFactoryPluginInterface {

  /**
   * Returns the label for use on the administration pages.
   *
   * @return string
   *   The administration label.
   */
  public function label();

  /**
   * Returns the plugin's description.
   *
   * @return string
   *   A string describing the plugin. Might contain HTML and should be already
   *   sanitized for output.
   */
  public function getDescription();

  /**
   * Informs the plugin that some of its dependencies are being removed.
   *
   * The plugin should attempt to change its configuration in a way to remove
   * its dependency on those items. However, to avoid problems, it should (as
   * far as possible) not add any new dependencies in the process, since there
   * is no guarantee that those are not currently being removed, too.
   *
   * @param object[][] $dependencies
   *   An array of dependencies, keyed by dependency type ("module", "config",
   *   etc.) and dependency name.
   *
   * @return bool
   *   Whether the dependency was successfully removed from the plugin – that
   *   is, after the configuration changes that were made, none of the removed
   *   items are dependencies of this plugin anymore.
   */
  public function onDependencyRemoval(array $dependencies);

}
