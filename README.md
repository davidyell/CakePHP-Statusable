CakePHP-Statusable
==================
A [CakePHP](http://www.cakephp.org/) plugin which will take out the hassle of managing the status of items in a web application. I am always adding a `Status` model and `status_id` fields to my models so that I can mark them as active or inactive. 

## What does it do?
The behaviour, when attached to a model, will deal with the filtering of finds automatically so that items which have specific statuses will not be displayed to users. It will also allow soft deletion by just changing the status of a record to 'deleted' rather than removing it.  

There is also a method to allow you to pull a list of statuses from the behaviour to display in your admin for users to change the status of a record.

##Assumptions
This plugin makes just one simple assumptions about your application.  
**You have an admin area to administer content, probably using the `admin` prefix**  
This assumption is because you will want to display some items to front-end users and some different items to the administrator users. The switching will work based on the routing prefix.

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

You'll need to create the fields for the `status_id` and also the `deleted_date` in your database tables, for each model to which you attach the behaviour. You can name these differently if you want to but you'll have to remember to configure them when you attach the behaviour to the model.  
Don't forget to set the `status_id` fields default value to your default status. For example `2` for `inactive`.  
Also the `deleted_date` field will need to be a `DATETIME` field.

###Attach the behaviour to your model
```php
<?php
// Model/Example.php
    public $actsAs = array(
        'Statusable.Statusable' // If you want to configure the behaviour you can pass options in here
    );
```
Configuration options can be passed to the behaviour here if your configuration differs from the default. You can find the default options in the behaviour source code in `$defaults`.

###Add component to controller
In your controller, you'll need to add the component. I'm hoping to be able to get rid of this at some point, but for the meantime, it's needed to get at the routing.
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
- [x] Something to speed up administration of records, like a component which injects a `status_id` field into forms. Might be too much and too narrow a use-case to be helpfull to everyone though.
- [x] Not too sure on having the statuses in the database or in the behaviour. If they are in the database how will you know which ones are displayable and where? __Allowed configuration in the behaviour__

##Honourable mentions
I also make use of the [CakeDC/Utils/SoftDelete](https://github.com/CakeDC/utils/blob/master/Model/Behavior/SoftDeleteBehavior.php) behaviour on a regular basis, so this takes inspiration from there. So a generous tip of the hat to the [CakeDC](http://github.com/cakedc) guys.