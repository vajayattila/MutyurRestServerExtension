# MutyurRestServerExtension
JSON rest server extension for Mütyür PHP Framework

# How to setup
- Download Mütyür PHP Framework from this link: https://github.com/vajayattila/MutyurPHPMVC/archive/master.zip
- Extract files from MutyurPHPMVC-master archive folder to your folder
- Download JSON rest server extension for Mütyür PHP Framework from this link: https://github.com/vajayattila/MutyurRestServerExtension/archive/master.zip
- Extract files from MutyurRestServerExtension-master archive folder to your folder. Overwrite defaultcontroller.php, config.php and readme.md files
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


