CakePHP-Statusable
==================

A behaviour to allow model records to have various statuses.

##Why?
I am always adding a `Status` model and `status_id` fields to my models so that I can mark them as active or inactive. It's time this functionality was made into a plugin!

##Proposed features
* Migration for schema of `statuses` table
* `Status` model
* Behaviour using model callbacks to change statuses
* Behaviour to modify finds to add the extra conditions
* Borrrow some functionality from the CakeDC/Utils/SoftDelete behaviour in relation to deletion
