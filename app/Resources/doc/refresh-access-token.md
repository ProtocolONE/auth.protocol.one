Refresh access token
====================

To receive or update the access token, you must send a GET request to the address
`/api/v1/token/refresh/{token}`. Where the `{token}` should be replaced by the content 
of the refresh token.

### HTTP codes
- 200 (ОК)
- 403 (Invalid or expired refresh token)

##### Example of success to get access token
Request headers

    Host: auth.protocolone.local
    Content-Type: application/json;charset=UTF-8
    
Response headers

    HTTP/1.1 200 OK
    Content-Type: application/json
    
Response body

    {
        "accessToken":{
            "value": "eyJ0eXAiOiJKV1QiLCJhbGci...r0uIGqEHTVlj3qIflUAlqwLKahqlHcCjgHsbMM5Ts",
            "exp": 1538207253
        }
    }

##### Example of failed to get access token
Request headers

    Host: auth.protocolone.local

Response headers

    HTTP/1.1 403 Forbidden
    Content-Type: application/json
    
Response body

    []
