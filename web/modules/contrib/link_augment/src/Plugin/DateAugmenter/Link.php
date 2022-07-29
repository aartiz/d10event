<?php

namespace Drupal\link_augment\Plugin\DateAugmenter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\date_augmenter\DateAugmenter\DateAugmenterPluginBase;
use Drupal\date_augmenter\Plugin\PluginFormTrait;

/**
 * Date Augmenter plugin to inject Registration links.
 *
 * @DateAugmenter(
 *   id = "link",
 *   label = @Translation("Links"),
 *   description = @Translation("Adds links to an event, for example to register."),
 *   weight = 0
 * )
 */
class Link extends DateAugmenterPluginBase implements PluginFormInterface {

  use PluginFormTrait;

  protected $processService;
  protected $config;
  protected $output;

  /**
   * Builds and returns a render array for the task.
   *
   * @param array $output
   *   The existing render array, to be augmented, passed by reference.
   * @param Drupal\Core\Datetime\DrupalDateTime $start
   *   The object which contains the start time.
   * @param Drupal\Core\Datetime\DrupalDateTime $end
   *   The optionalobject which contains the end time.
   * @param array $options
   *   An array of options to further guide output.
   */
  public function augmentOutput(array &$output, DrupalDateTime $start, DrupalDateTime $end = NULL, array $options = []) {
    $config = $options['settings'] ?? $this->getConfiguration();
    $end_fallback = $end ?? $start;
    $now = new DrupalDateTIme();
    if ($end_fallback < $now && !$config['past_events']) {
      return;
    }
    $uri = $config['link_url'];
    $text = $config['link_text'];
    $entity = $options['entity'] ?? NULL;
    // Replace any token values provided.
    if ($entity && \Drupal::hasService('token')) {
      $token_service = \Drupal::service('token');
      $token_data = [
        $entity->getEntityTypeId() => $entity,
      ];
      $text = $token_service->replace($text, $token_data);
      $uri = $token_service->replace($uri, $token_data);
    }
    $uri = UrlHelper::stripDangerousProtocols($uri);
    $urlObj = \Drupal::service('path.validator')->getUrlIfValid($uri);
    if (!$urlObj) {
      return;
    }

    $output['link'] = [
      '#title' => Xss::filter($text),
      '#type' => 'link',
      '#url' => $urlObj,
      '#prefix' => ' ',
      '#suffix' => ' ',
    ];

    // Parse and sanitize provided classes.
    if ($config['class']) {
      $classes = explode(' ', $config['class']);
      foreach ($classes as $index => $class) {
        $classes[$index] = Html::getClass($class);
      }
      $output['link']['#attributes']['class'] = $classes;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'link_text' => 'Register Now!',
      'link_url' => '<front>',
      'class' => '',
      'past_events' => FALSE,
    ];
  }

  /**
   * Create configuration fields for the plugin form, or injected directly.
   *
   * @param array $form
   *   The form array.
   * @param array $settings
   *   The setting to use as defaults.
   * @param mixed $field_definition
   *   A parameter to define the field being modified. Likely FieldConfig.
   *
   * @return array
   *   The updated form array.
   */
  public function configurationFields(array $form, ?array $settings, $field_definition) {
    if (empty($settings)) {
      $settings = $this->defaultConfiguration();
    }
    $form['link_text'] = [
      '#title' => $this->t('Link text'),
      '#type' => 'textfield',
      '#default_value' => $settings['link_text'],
      '#description' => $this->t('The text to use in the registration link. You can use static text or tokens.'),
      '#required' => TRUE,
    ];

    $form['link_url'] = [
      '#title' => $this->t('Link destination'),
      '#type' => 'textfield',
      '#default_value' => $settings['link_url'],
      '#description' => $this->t('Where the link will take the user. You can use a static URI or tokens.'),
      '#required' => TRUE,
    ];

    $form['class'] = [
      '#title' => $this->t('Class'),
      '#description' => $this->t('A CSS class to apply to the link. If using multiple classes, separate them by spaces.'),
      '#type' => 'textfield',
      '#default_value' => $settings['class'],
    ];

    $form['past_events'] = [
      '#title' => $this->t('Show link for past events?'),
      '#type' => 'checkbox',
      '#default_value' => $settings['past_events'],
    ];

    // TODO: Allow choice of a webform? Or maybe provide a link field?

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $this->configurationFields($form, $this->configuration);

    return $form;
  }

}
