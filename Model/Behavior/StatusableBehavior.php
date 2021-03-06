<?php
App::uses('ModelBehavior', 'Model');

/**
 * A behaviour for CakePHP to take away some of the workload in managing the 
 * status of items in a web app.
 *
 * @author David Yell <neon1024@gmail.com>
 */

class StatusableBehavior extends ModelBehavior {
    
/**
 * Default settings for the behaviour
 * 
 * @var array
 * 
 * TODO: Tidy these options up, don't think there needs to be this many
 */
    public $defaults = array(
        'statusModel' => 'Status',      // The model name following convention
        'fields' => array(
            'status' => 'status_id',        // The foreign key field in your db
            'deletedDate' => 'deleted_date' // The deletion date field name
        ),
        'statuses' => array(
            'displayed' => array(
                1 => 'Live',            // Displayed on the site, everyone can see it
                3 => 'Protected live',  // Cannot be deleted, but is displayed
            ),
            'adminOnly' => array(
                2 => 'Inactive',            // Only displayed to administrators
                4 => 'Protected inactive',  // Cannot be deleted, but is not displayed
                5 => 'Archived',            // No longer in use but might be needed
            ),
            'deleted' => array(
                6 => 'Deleted'  // Will not display to admins or users
            )
        ),
        'modifyModified' => true,   // Should the behaviour change the modified date when updating records?
        'adminPrefix' => 'admin',   // What is the name of your admin prefix?
		'protected' => [3, 4]		// Statuses which are protected from deletion
    );
    
/**
 * Where we can store the models specific settings
 * 
 * @var array
 */
    public $settings = array();
	
/**
 * Store an instance of the model configured for Statuses
 * 
 * @var Model $status
 */
	protected $Status = null;
	
/**
 * Store the name of the model to which this behaviour has been attached
 * 
 * @var Model $modelAlias
 */
	protected $modelAlias = null;
    
/**
 * Return the configured statuses as an array from the merged settings
 * 
 * @return array
 */
	public function getStatuses() {
		return $this->settings[$this->modelAlias]['statuses'];
	}
	
/**
 * Setup the behaviour and merge in the settings. Check that the model has the
 * required fields
 * 
 * @param Model $model
 * @param array $config
 */
    public function setup(Model $model, $config = array()) {
        parent::setup($model, $config);
        
        $this->settings[$model->alias] = array_merge($this->defaults, $config);
		
		$this->modelAlias = $model->alias;
        $this->hasField($model);
		$this->checkPrefix($model);
		
		if (!ClassRegistry::isKeySet($this->settings[$model->alias]['statusModel'])) {
			$this->Status = ClassRegistry::init($this->settings[$model->alias]['statusModel']);
		}
    }
	
/**
 * Ensure that the current model actually has the fields which are configured
 * for the status.
 * 
 * @param Model $model
 * @return void
 */
	protected function hasField(Model $model) {
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!$model->hasField($field)) {
                trigger_error(__($model->alias . " model doesn't have the field " . $field));
                return;
            }
        }
	}
	
/**
 * Ensure that the configured prefix exists in the core Routing.prefixes config
 * 
 * @param Model $model
 * @return void
 */
	protected function checkPrefix(Model $model) {
		$found = false;
		foreach (Configure::read('Routing.prefixes') as $prefix) {
			if ($prefix === $this->settings[$model->alias]['adminPrefix']) {
				$found = true;
			}
		}
		if (!$found) {
			trigger_error(__("Configured prefix '{$this->settings[$model->alias]['adminPrefix']}' doesn't exist in Routing.prefixes configuration."));
			return;
		}
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
        $liveConditions = array($model->alias . '.status_id' => array_keys($this->settings[$model->alias]['statuses']['displayed']));
        $adminConditions = array($model->alias . '.status_id !=' => key($this->settings[$model->alias]['statuses']['deleted']));

		if (!isset($query['conditions'])) {
			$query['conditions'] = array();
		}
		
        if (isset($model->prefix) && $model->prefix == $this->settings[$model->alias]['adminPrefix']) {
            $query['conditions'] = array_merge($query['conditions'], $adminConditions);
            
            // Join the Status model for easy front-end display
            $query['joins'] = array_merge($query['joins'], array(
                array(
                    'table' => $this->Status->useTable,
                    'alias' => $this->settings[$model->alias]['statusModel'],
                    'type' => 'LEFT',
                    'conditions' => array(
                        $model->alias . '.' . $this->settings[$model->alias]['fields']['status'] . ' = ' . $this->settings[$model->alias]['statusModel'] . '.id'
                    )
                )
            ));
            
            if ($query['fields'] === null) {
                $query['fields'] = array('*');
            }

            if (!is_string($query['fields'])) {
                $query['fields'] = array_merge($query['fields'], array(
                    $this->settings[$model->alias]['statusModel'] . '.id',
                    $this->settings[$model->alias]['statusModel'] . '.name',
                ));
            }
        } else {
            $query['conditions'] = array_merge($query['conditions'], $liveConditions);
        }
        
        return $query;
    }
	
/**
 * Catch the request to delete and route to our own delete method
 * 
 * // TODO: Try and remove this, as the controller should be calling the delete
 * method in the model, which this should overwrite
 * 
 * // TODO: Think about the return here as returning true will continue the
 * delete and will probably be destructive
 * 
 * @param Model $model
 * @param bool $cascade
 * @return bool
 */
	public function beforeDelete(Model $model, $cascade = true) {
		parent::beforeDelete($model, $cascade);
		
		$this->delete($model, $model->data[$model->alias]['id'], $cascade);
		return false;
	}

/**
 * Overwrite the delete method so that we can run an update instead
 * 
 * @param Model $model
 * @param int $id
 * @param bool $cascade
 * @return bool
 */
    public function delete(Model $model, $id = null, $cascade = true) {
		if (!empty($id)) {
			$model->id = $id;
		}
		$id = $model->id;
		
		$model->recursive = -1;
		$record = $model->find('first', array(
			'conditions' => array(
				$model->alias . '.' . $model->primaryKey => $id
			)
		));
		
		if ($this->checkProtected($model, $id)) {
			$model->set($record);
			$model->set($this->settings[$model->alias]['fields']['status'], key($this->settings[$model->alias]['statuses']['deleted']));
			$model->set($this->settings[$model->alias]['fields']['deletedDate'], date('Y-m-d H:i:s'));
			return (bool)$model->save();
		}
		
		return false;
	}
	
/**
 * Check if an item can be deleted or if it's protected
 * 
 * @param Model $model
 * @param int $id
 * @return boolean
 */
	protected function checkProtected(Model $model, $id) {
        $item = $model->find('first', array(
            'conditions' => array(
                $model->alias . '.' . $model->primaryKey => $id
            )
        ));
        
        if (in_array($item[$model->alias][$this->settings[$model->alias]['fields']['status']], $this->settings[$model->alias]['protected'])) {
            return false;
        }
        
        return true;
	}
}
