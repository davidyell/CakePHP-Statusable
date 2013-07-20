<?php
/**
 * A behaviour for CakePHP to take away some of the workload in managing the 
 * status of items in a web app.
 *
 * @author David Yell <neon1024@gmail.com>
 */

App::uses('ModelBehavior', 'Model');

class StatusableBehavior extends ModelBehavior {
    
/**
 * Default settings for the behaviour
 * 
 * @var array
 * 
 * TODO: Tidy these options up, don't think there needs to be this many
 */
    public $defaults = array(
        'statusTable' => 'statuses',    // The name of the table in the db
        'statusModel' => 'Status',      // The model name matching the db table
        'fields' => array(
            'status' => 'status_id',    // The foreign key field in your db
            'deletedDate' => 'deleted_date' // The deletion date field name
        ),
        'statuses' => array(
            1 => 'Live',        // Displayed on the site, everyone can see it
            2 => 'Inactive',    // Only displayed to administrators
            3 => 'Protected live',   // Cannot be deleted, but is displayed
            4 => 'Protected inactive',   // Cannot be deleted, but is displayed
            5 => 'Archived',    // No longer in use but might be needed
            6 => 'Deleted'      // Will not display to admins or users
        ),
        'modifyModified' => true,   // Should the behaviour change the modified date when updating records?
        'adminPrefix' => 'admin'    // What is the name of your admin prefix?
    );
    
/**
 * Where we can store the models specific settings
 * 
 * @var array
 */
    public $settings = array();
    
/**
 * Setup the behaviour and merge in the settings. Check that the model has the
 * required fields
 * 
 * @param Model $model
 * @param array $config
 */
    public function setup(Model $model, $config = array()) {
        parent::setup($model, $config);
        
        $this->settings[$model->alias] = array_merge($this->defaults, $this->settings);
        
        // Check that the model has the required fields
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!$model->hasField($field)) {
                trigger_error($model->alias . " model doesn't have the field " . $field);
                return;
            }
        }
    }
    
/**
 * Catch the delete and prevent it to run an update instead to change the status.
 * Also deals with protected items
 * 
 * @param Model $model
 * @param boolean $cascade
 * @return boolean
 */
    public function beforeDelete(Model $model, $cascade = true) {
        
        
        return false;
    }

/**
 * Catch any finds and automagically insert extra conditions to strip out items
 * of certain statuses based on who should be looking at the data
 * 
 * @param Model $model
 * @param array $query
 * @return array
 */
    public function beforeFind(Model $model, $query) {
        $live = array('status_id' => array(1,3));
        $admin = array('status_id !=' => 6);
        
        if (isset($model->prefix) && $model->prefix == $this->settings[$model->alias]['adminPrefix']) {
            $query['conditions'] = array_merge($query['conditions'], $admin);
        } else {
            $query['conditions'] = array_merge($query['conditions'], $live);
        }
        
        // Add Status title
        $query['joins'] = array_merge($query['joins'], array(
            array(
                'table' => $this->settings[$model->alias]['statusTable'],
                'alias' => $this->settings[$model->alias]['statusModel'],
                'type' => 'LEFT',
                'conditions' => array(
                    $model->alias . '.' . $this->settings[$model->alias]['fields']['status'] . ' = ' . $this->settings[$model->alias]['statusModel'] . '.id'
                )
            )
        ));
        
        // Add status fields to the select
        $statusFields = array(
            $this->settings[$model->alias]['statusModel'] . '.id',
            $this->settings[$model->alias]['statusModel'] . '.name',
        );
        
        if ($query['fields'] === null) {
            $query['fields'] = array('*');
        }
        
        if (!is_string($query['fields'])) {
            $query['fields'] = array_merge($query['fields'], $statusFields);
        }
        
        return $query;
    }

    
}