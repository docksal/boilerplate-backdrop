<?php
/**
 * @file
 * Definition of views_handler_filter_history_user_timestamp.
 */

/**
 * Filter for new content.
 *
 * The handler is named history_user, because of compatibility reasons, the
 * table is history.
 *
 * @ingroup views_filter_handlers
 */
class views_handler_filter_history_user_timestamp extends views_handler_filter {
  // Don't display empty space where the operator would be.
  var $no_operator = TRUE;

  function expose_form(&$form, &$form_state) {
    parent::expose_form($form, $form_state);
    // @todo There are better ways of excluding required and multiple (object flags)
    unset($form['expose']['required']);
    unset($form['expose']['multiple']);
    unset($form['expose']['remember']);
  }

  function value_form(&$form, &$form_state) {
    // Only present a checkbox for the exposed filter itself. There's no way
    // to tell the difference between not checked and the default value, so
    // specifying the default value via the views UI is meaningless.
    if (!empty($form_state['exposed'])) {
      if (isset($this->options['expose']['label'])) {
        $label = $this->options['expose']['label'];
      }
      else {
        $label = t('Has new content');
      }
      $form['value'] = array(
        '#type' => 'checkbox',
        '#title' => $label,
        '#default_value' => $this->value,
      );
    }
  }

  function query() {
    global $user;
    // This can only work if we're logged in.
    if (!$user || !$user->uid) {
      return;
    }

    // Don't filter if we're exposed and the checkbox isn't selected.
    if ((!empty($this->options['exposed'])) && empty($this->value)) {
      return;
    }

    // Backdrop kills old history, so nodes that haven't been updated since
    // NODE_NEW_LIMIT are filtered out.
    $limit = REQUEST_TIME - NODE_NEW_LIMIT;

    $this->ensure_my_table();
    $field = "$this->table_alias.$this->real_field";
    $node = $this->query->ensure_table('node', $this->relationship);

    $clause = '';
    $clause2 = '';
    if (module_exists('comment')) {
      $ncs = $this->query->ensure_table('node_comment_statistics', $this->relationship);
      $clause = ("OR $ncs.last_comment_timestamp > (***CURRENT_TIME*** - $limit)");
      $clause2 = "OR $field < $ncs.last_comment_timestamp";
    }

    // NULL means a history record doesn't exist. That's clearly new content.
    // Unless it's very very old content. Everything in the query is already
    // type safe cause none of it is coming from outside here.
    $this->query->add_where_expression($this->options['group'], "($field IS NULL AND ($node.changed > (***CURRENT_TIME*** - $limit) $clause)) OR $field < $node.changed $clause2");
  }

  function admin_summary() {
    if (!empty($this->options['exposed'])) {
      return t('exposed');
    }
  }
}
