Registration by username and password
=====================================

To register a user, send a POST request to the address `/api/v1/user/create` and pass the 
required parameters:
- email `string`
- password `string` (must contain at least 6 characters)
- readEula `boolean` (`true` to confirm the agreement)

If the registration is successful, refreshToken and accessToken tokens will return, 
as well as the end time, otherwise have error message.

###HTTP codes
- 200 (ОК)
- 400 (Error in query parameters)

#####Example of success registration
Request headers

    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwerty", 
        "readEula": true
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

#####Example of failed registration
Request headers

    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Request body

    {
        "email": "test@domain.com", 
        "password": "qwert", 
        "readEula": false
    }
    
Response headers

    HTTP/1.1 400 Bad Request
    Content-Type: application/json
    
Response body

    {
        "email": "User with email \u0022test@domain.com\u0022 already exist.",
        "password": "Password must contain at least 6 characters."
        "readEula": "You must accept EULA and Privacy Policy to continue."
    }
