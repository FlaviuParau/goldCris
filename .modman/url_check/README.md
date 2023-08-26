Blugento UrlCheck module

Check if created Product or Category have an url key that already exist and do not let the admin user to insert duplicate URL's.

If the module is deleted from a env where was previously installed you need to delete the backend_model of the 'name' attribute(type product), manually from 'eav_attribute' table or you can run this SQL::

UPDATE eav_attribute SET backend_model = null WHERE entity_type_id = 4 AND attribute_code = 'name'