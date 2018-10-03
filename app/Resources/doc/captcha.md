Captcha
=======

To get the authorization captcha, you need to make a GET request to the address 
`/api/v1/captcha/login` and pass the required parameters:
- email `string`

and one optional parameter
- mode `string`

The `mode` parameter is used to select the mode of the image. It can be obtained in 
the following format:
- get (in binary format, by default)
- inline (in base64 format)


### HTTP codes
- 200 (ОК)

##### Example of get captcha in inline mode
Request headers

    GET: /api/v1/captcha/login?email=test@domain.com&mode=inline
    Host: auth.protocolone.local
    
Response headers

    HTTP/1.1 200 OK
    Content-Type: image/jpeg
    
Response body

    data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2OTApLCBxdWFsaXR5ID0gO
