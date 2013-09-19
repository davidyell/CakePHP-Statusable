CakePHP-Statusable
==================

A behaviour to allow model records to have various statuses.  
This is **alpha** and is currently under development.

##Why?
I am always adding a `Status` model and `status_id` fields to my models so that I can mark them as active or inactive.  
It's time this functionality was made into a plugin!  
I also make use of the [CakeDC/Utils/SoftDelete](https://github.com/CakeDC/utils/blob/master/Model/Behavior/SoftDeleteBehavior.php) behaviour on a regular basis, so this takes inspiration from there. So a generous tip of the hat to the [CakeDC](http://github.com/cakedc) guys.

##Assumptions
* You have an admin area to administer content, probably using the `admin` prefix
* You have regular users and administrator users

##Configuration
You need to load the plugin in your `app/Config/bootstrap.php` using `CakePlugin::load('Statusable')`. You can use `CakePlugin::loadAll()` but it's slow.  

###Database
You will also need a table to store your statuses. You can create the default using the following.  
```bash
$ Console/cake schema create -p Statusable
```
Once the table is created, you'll need to populate it with some statuses for the behaviour to use. An example might be,
```sql
INSERT INTO `statuses` (name, created, modified) VALUES
('Live', NOW(), NOW()),
('Inactive', NOW(), NOW()),
('Protected live', NOW(), NOW()),
('Protected inactive', NOW(), NOW()),
('Archived', NOW(), NOW()),
('Deleted', NOW(), NOW())
```

You'll need to create the fields for the `status_id` and also the `deleted_date` in your database tables. You can name these differently if you want to but you'll have to remember to configure them when you attach the behaviour to the model.  
Don't forget to set the `status_id` fields default value to your default status. For example `2` for `inactive`.

###Attach the behaviour to your model
```php
<?php
// Model/Example.php
    public $actsAs = array(
        'Statusable.Statusable'
    );
```
Configuration options can be passed to the behaviour here if your configuration differs from the default. You can find the default options in the behaviour source code in `$defaults`.

###Add component to controller
In your controller, you'll need to add the component.
```php
<?php
// Controller/ExampleController.php
    public $components = array(
        'Statusable.Statusable'
    );
```

##Proposed features
- [ ] Migration for schema of `statuses` table
- [ ] `Status` model
- [x] Behaviour using model callbacks to change statuses
- [x] Behaviour to modify finds to add the extra conditions
- [x] Borrrow some functionality from the CakeDC/Utils/SoftDelete behaviour in relation to deletion
- [ ] Something to speed up administration of records, like a component which injects a `status_id` field into forms. Might be too much and too narrow a use-case to be helpfull to everyone though.
- [x] Not too sure on having the statuses in the database or in the behaviour. If they are in the database how will you know which ones are displayable and where? __Allowed configuration in the behaviour__
