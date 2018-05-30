# MutyurRestServerExtension
JSON rest server extension for Mütyür PHP Framework

# How to setup
- Download Mütyür PHP Framework from this link: https://github.com/vajayattila/MutyurPHPMVC/archive/master.zip
- Extract files from MutyurPHPMVC-master archive folder to your folder
- Download JSON rest server extension for Mütyür PHP Framework from this link: https://github.com/vajayattila/MutyurRestServerExtension/archive/master.zip
- Extract files from MutyurRestServerExtension-master archive folder to your folder. Overwrite readme.md file.
# Setup config.php
In the application folder you can see this files:
- config.php
- config_rest_sample.php
Find the next line in the confog_rest_sample.php and copy it:
```php
/** @brief routes*/
$config['routes']=array(
	.
	.
	'service' => 'defaultcontroller/service', // for rest service		
);		
```
Open the config.php and paste this line into the routes block. Here:
```php
$config['routes']=array(
	'default' => 'defaultcontroller/index',	
	'test' => 'defaultcontroller/test',
	'service' => 'defaultcontroller/service', // for rest service		<- new route for the rest service
);	
```


















# Test it
- Start php embed web server in your folder:
```
php -S 127.0.0.1:8001
```
then you can test rest server version with GET method. Write to your browser:
```html
http://127.0.0.1:8001/service?action=restsrv_getversion
```
if everything is right you will see this output in your browser (raw data):
```json
{
    "name": "demorestserver",
    "version": "1.0.0.0"
}
```


