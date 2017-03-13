<?php

namespace Drupal\template_whisperer\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Template Whisperer Entity entity.
 *
 * @ingroup template_whisperer
 *
 * @ContentEntityType(
 *   id = "template_whisperer",
 *   label = @Translation("Template Whisperer Entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\template_whisperer\Form\TemplateWhispererForm",
 *       "edit" = "Drupal\template_whisperer\Form\TemplateWhispererForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\template_whisperer\TemplateWhispererEntityAccessControlHandler",
 *   },
 *   base_table = "template_whisperer",
 *   admin_permission = "administer template_whisperer entities",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/template-whisperer/{template_whisperer}",
 *     "add-form" = "/admin/structure/template-whisperer/add",
 *     "edit-form" = "/admin/structure/template-whisperer/{template_whisperer}/edit",
 *     "delete-form" = "/admin/structure/template-whisperer/{template_whisperer}/delete",
 *     "collection" = "/admin/structure/template-whisperer/list",
 *   },
 * )
 */
class TemplateWhispererEntity extends ContentEntityBase implements TemplateWhispererEntityInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSuggestion() {
    return $this->get('suggestion')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSuggestion($suggestion) {
    $this->set('suggestion', $suggestion);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Template Whisperer. (Will appear in the field widget)'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['suggestion'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Theme Suggestion'))
      ->setDescription(t("The Theme Suggestion machine name that should be used. E.g. <code>news_list</code>"))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}