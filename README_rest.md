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
# Add the service function to defaultcontroller
In the application/controllers you can see this files:
- defaultcontroller.php
- defaultcontroller_rest_sample.php
Find the service function in defaultcontroller_rest_sample.php and copy it:
```php
	.
	.
	public function service(){ // for restservice
		/*
			Examples: 
			For GET methods:
			http://127.0.0.1:8001/service?action=restsrv_getversion
			http://127.0.0.1:8001/service?action=echo&message=helloworld

			For POST method with curl:
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "restsrv_getversion"}'
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "echo","message": "Hello World!"}'

		*/
		$this->m_restserver->execute(); 
	}
	.
	.
```
Open the defaultcontroller.php and paste it. Here:
```php
class defaultcontroller extends workframe{
	.
	.
	.
	public function service(){ // for restservice 
		/*
			Examples: 
			For GET methods:
			http://127.0.0.1:8001/service?action=restsrv_getversion
			http://127.0.0.1:8001/service?action=echo&message=helloworld

			For POST method with curl:
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "restsrv_getversion"}'
			curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "echo","message": "Hello World!"}'
			
		*/
		$this->m_restserver->execute(); 
	}	
	.
	.
	.
}
```
# Load demorestserver extension 
Open the defaultcontroller_rest_sample.php. Find and copy the this line:
```php
$this->restserver=$this->load_extension('demorestserver');	// for restserver
```
Open the defaultcontroller.php and paste it here:
```php
	public function __construct(){
		.
		.
		.		
		$this->m_restserver=$this->load_extension('demorestserver');	// for restserver
	}
```
And define member variable:
```php
.
.
.
class defaultcontroller extends workframe{
	protected $m_lang;
	protected $m_model;
	protected $m_session;
	protected $m_restserver; // for restserver
.
.
.
```
# Test it
- Start php embed web server in your folder:
```
php -S 127.0.0.1:8001
```
then you can test rest server version with GET method. Write to your browser:
```php
http://127.0.0.1:8001/service?action=restsrv_getversion // restsrv_getversion is defined in application/extension/restserver.php
or
http://127.0.0.1:8001/service?action=echo&message=Hello%20World // echo is defined in application/extension/demorestserver.php

```
if everything is right you will see this output in your browser (raw data):
```json
{
    "name": "demorestserver",
    "version": "1.0.0.0"
}

or 

{
    "echo": "Hello World"
}
```
# Test simple field validation
In the echo method's message field get a regex pattern in the demonstration rest service:
```php
    protected function echo(){
        $return=TRUE;        
        $echo=$this->getfieldvalue('message', TRUE, '^.{5,20}$'); // Min length is 5, max length is 20, required
   .
   .
   .
```
So we can testing wrong cases too. 
## Testing wrong cases with GET method
Input:
```
http://127.0.0.1:8001/service?action=echo
```
Output:
```
Missing field: message
```

Input:
```
http://127.0.0.1:8001/service?action=echo&message=1234 <- too short message
```
Output:
```
Pattern not match in 'message' field. The value is '1234'. The pattern is '/^.{5,20}$/'
```

Input:
```
http://127.0.0.1:8001/service?action=echo&message=123456789012345678901 <- too long message
```
Output:
```
Pattern not match in 'message' field. The value is '1234'. The pattern is '/^.{5,20}$/'
```
## Testing wrong cases with POST method
You can use the service route also use for the POST methods calling
```
http://127.0.0.1:8001/service
```
POST request:
```
{
  "action": "echo",
  "message": "Hello World",
  "partners": [
    {
      "name": "Vajay Attila"
    },
    {
      "name": "Mütyürke", <- syntax error
    }
  ]
}
```
Output:
```
JSON decode status: Syntax error
```
POST request:
```
{
  "action": "echo",
  "message": "Hello World",
  "partners": [
    {
      "name": "Vajay Attila"
    },
    {
      "namex": "Mütyürke", <- wrong field name
    }
  ]
}
```
Output:
```
Missing field: name
in partners array on index: 1
```
POST request:
```
{
  "action": "echo",
  "message": "Hello World",
  "partners": [
    {
      "name": "Vajay Attila"
    },
    {
      "name": "Mütyür", <- too short value
    }
  ]
}
```
Output:
```
Pattern not match in 'name' field. The value is 'Mütyür'. The pattern is '/^.{10,30}$/'
in partners array on index: 1
```
POST request:
```
{
  "action": "echo",
  "message": "Hello World",
  "partners": [
    {
      "name": "Vajay Attila3456789012345678901" <- too long value
    },
    {
      "name": "Mütyürke"
    }
  ]
}
```
Output:
```
Pattern not match in 'name' field. The value is 'Vajay Attila3456789012345678901'. The pattern is '/^.{10,30}$/'
in partners array on index: 0
```
The good case finally:
```
{
  "action": "echo",
  "message": "Hello World",
  "partners": [
    {
      "name": "Vajay Attila" <- too long value
    },
    {
      "name": "Mütyürke"
    }
  ]
}
```
Output:
```
{
    "echo": "Hello World",
    "partner": [
        {
            "name": "Vajay Attila"
        },
        {
            "name": "M\u00fcty\u00fcrke"
        }
    ]
}
```

