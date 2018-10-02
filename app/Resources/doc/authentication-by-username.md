Authentication by username and password
=======================================

To authenticate a user, send a POST request to the address `/api/v1/user/login` and pass the 
required parameters:
- email `string`
- password `string`

and one optional parameter
- captcha `string`

If the authentication is successful, refreshToken and accessToken tokens will return, 
as well as the end time, otherwise have error message.

After three (default) unsuccessful authentication attempts, you need to transfer the 
captcha value. If unsuccessful authentication occurred 10 times, then the account is 
blocked by the possibility of authentication for a period of 10 minutes (by default).

How to get captcha? Find out on [this page](captcha.md).

### HTTP codes
- 200 (ОК)
- 400 (Invalid username or password)
- 429 (Captcha require or invalid captcha)
- 426 (Temporary lock)

##### Example of success registration
Request headers

    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwerty", 
        "captcha": ""
    }
    
Response headers

    HTTP/1.1 200 OK
    Content-Type: application/json
    
Response body

    {
        "accessToken":{
            "value": "eyJ0eXAiOiJKV1QiLCJhbGci...r0uIGqEHTVlj3qIflUAlqwLKahqlHcCjgHsbMM5Ts",
            "exp": 1538207253
        },
        "refreshToken":{
            "value": "c20385f75297cc22677e8dc3...48ef239bab2497ccab58c55d4bd81a72ccdef5df6",
            "exp": 1569762379
        }
    }

##### Example of failed registration
Request headers

    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwert", 
        "captcha": ""
    }
    
Response headers

    HTTP/1.1 400 Bad Request
    Content-Type: application/json
    
Response body

    {
        "message": "Invalid username or password"
    }
