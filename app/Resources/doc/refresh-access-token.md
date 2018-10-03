Refresh access token
====================

To receive or update the tokens, you must send a GET request to the address
`/api/v1/token/refresh/{token}`. Where the `{token}` should be replaced by the content 
of the refresh token. You will receive a new accessToken and a refreshToken, which will 
need to be used in further requests. The old refreshToken will no longer work.

### HTTP codes
- 200 (ОК)
- 403 (Invalid or expired refresh token)

##### Example of success to get access token
Request headers

    GET: /api/v1/token/refresh/asd79087vb98z7fxcvb876987d6f8g69c8v7b6xc
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
        },
        "refreshToken":{
            "value": "c20385f75297cc22677e8dc3...48ef239bab2497ccab58c55d4bd81a72ccdef5df6",
            "exp": 1569762379
        }
    }

##### Example of failed to get access token
Request headers

    GET: /api/v1/token/refresh/asd79087v
    Host: auth.protocolone.local

Response headers

    HTTP/1.1 403 Forbidden
    Content-Type: application/json
    
Response body

    []
