<?php

/**
 * @file
 * Contains ShoovJsLmIncidentsResource.
 */

class ShoovJsLmIncidentsResource extends \ShoovEntityBaseNode {

  /**
   * Overrides \RestfulEntityBaseNode::publicFieldsInfo().
   */
  public function publicFieldsInfo() {
    $public_fields = parent::publicFieldsInfo();

    $public_fields['build'] = array(
      'property' => 'field_js_lm_build',
    );

    $public_fields['user_id'] = array(
      'property' => 'field_user_id',
    );

    $public_fields['errors'] = array(
      'property' => 'field_js_lm_errors',
    );

    $public_fields['image'] = array(
      'property' => 'field_js_lm_image',
      'process_callbacks' => array(
        array($this, 'imageProcess'),
      ),
    );

    return $public_fields;
  }

  /**
   * Overrides \ShoovEntityBaseNode::checkEntityAccess().
   *
   * Always grant access to create.
   *
   * @todo: Reconsider.
   */
  protected function checkEntityAccess($op, $entity_type, $entity) {
    return TRUE;
  }

  public function entityPreSave(\EntityMetadataWrapper $wrapper) {
    parent::entityPreSave($wrapper);

    $request = $this->getRequest();

    // Add label.
    $wrapper->title->set($request['build'] . ' ' . time());

    // Add group reference
    $node_wrapper = entity_metadata_wrapper('node', $request['build']);
    $wrapper->js_lm->set($node_wrapper->js_lm->value(array('identifier' => TRUE)));
  }

  /**
   * Overrides \ShoovEntityBaseNode::checkPropertyAccess().
   *
   * Always allow to set properties.
   */
  protected function checkPropertyAccess($op, $public_field_name, EntityMetadataWrapper $property_wrapper, EntityMetadataWrapper $wrapper) {
    return TRUE;
  }

  /**
   * Overrides \ShoovEntityBaseNode::createEntity().
   *
   * Create file from Data URL before creating entity.
   */
  public function createEntity() {
    $request = $this->getRequest();

    // Get the  file contents from the Data URL.
    list($meta, $content) = explode(',', $request['image']);
    // Replace spaces with "+" since javascript puts spaces in the encoded data.
    $content = base64_decode(str_replace(' ', '+', $content));

    // Save the image.
    $filename = md5('JSLM-' . $request['build'] . '-incident-' . time());
    $file = file_save_data($content, 'public://' . $filename . '.png');

    // Replace the Data URL with the file ID in the request.
    $request['image'] = $file->fid;
    $this->setRequest($request);

    parent::createEntity();
  }
}
