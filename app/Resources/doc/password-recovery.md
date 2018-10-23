Password recovery
=================

To send the password recovery code, you need to send a GET request to the address 
`/api/v1/user/send-email/forgot/` and pass the required parameters:
- email `string`

At the specified email address will come a letter, which contains the code for changing 
the password. To change the user's password, you must send a POST request to the address 
`/api/v1/user/change-password/` and pass the required parameters:
- email `string`
- password `string` (new user password)
- code `string` (code from the letter)

### HTTP codes
- 200 (ОК)
- 400 (Error in query parameters)

##### Example of success change password
Request headers

    POST: /api/v1/user/change-password/
    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwerty", 
        "code": "399a0e80-cceb-43f3-812e-c316e833486f"
    }
    
Response headers

    HTTP/1.1 200 OK
    Content-Type: application/json
    
Response body

    {
        "result": true
    }

##### Example of failed change password
Request headers

    POST: /api/v1/user/change-password/
    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwert", 
        "code": "399a0e80-cceb-43f3-812e-c316e833486f"
    }
    
Response headers

    HTTP/1.1 400 Bad Request
    Content-Type: application/json
    
Response body

    {
        "password": "Password must contain at least 6 characters."
    }
