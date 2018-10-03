Registration and authentication over oAuth
==========================================

The user can register and log in using external accounts that implement the oAuth protocol.
The list of available services can be requested by `/api/v1/oauth/sources`.

In the configuration file `app/config/config.yml`, arrange the section` hwi_oauth.resource_owners` and 
specify the current settings for the authentication sources there. In the file `app/config/parameters.yml`, 
change the `oauth.sources` section, only these providers will be given when calling the 
`/api/v1/oauth/sources` method.

You can configure the authorization domain for the domain `auth.protocolone.local` and use the test 
settings from the configuration file. The list of test applications for different sources will be updated, 
but they will all be tied to the domain `auth.protocolone.local`.

##### Example of list oAuth services
Request headers

    GET: /api/v1/oauth/sources
    Host: auth.protocolone.local
    
Response headers

    HTTP/1.1 200 Bad Request
    Content-Type: application/json
    
Response body

    [
        {
            "name":"facebook",
            "url":"\/oauth\/connect\/facebook"
        },
        {
            "name":"vkontakte",
            "url":"\/oauth\/connect\/vkontakte"
        }
    ]

#### Authentication
To authenticate to the end service, open a new browser window with the address 
corresponding to the service. Specify the GET parameter `_destination` according to how 
you will handle the authentication response. Currently there are two message transport 
options supported: `JavaScript PostMessage` and` WebSocket`. 
The value of the parameter `_destination` must be encoded, for example, through 
[encodeURIComponent] (https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent) 
for the correct transmission of control characters.

For example: the `_destination` value for getting a response using PostMessage should be 
`/api/v1/oauth/result/postmessage`. The encoded string will look like 
`%2Fapi%2Fv1%2Foauth%2Fresult%2Fpostmessage`.

When using `WebSocket`, you must pass the` wsUrl` parameter, which contains the url with 
the `WebSocket` server, to which the response will be sent.

When using `PostMessage`, the response will be sent to the parent window.

The response will be transmitted as a JSON object converted to a string and will have 
the following structure:
    
    {
        "event": "eventName",
        "message": []
    }

 The `event` property can take the values` oauthCompletedWithError` and 
 `oauthCompletedSuccessfully` depending on the authentication result. If authentication 
 is successful, an array with tokens will be located in `message`, as well as in 
 authentication by [login and password](authentication-by-username.md).
 
##### Example of oAuth authentication by VKontakte with WebSocket
Request headers

    GET: /oauth/connect/vkontakte?_destination=%2Fapi%2Fv1%2Foauth%2Fresult%2Fwebsocket%3FwsUrl%3Dwss%3A%2F%2F127.0.0.1%3A123%2F
    Host: auth.protocolone.local
    
Response object (without `JSON.stringify`)

    {
        "event": 'oauthCompletedSuccessfully',
        "message": {
            "accessToken": {
                "value": "eyJ0eXAiOiJKV1QiLCJhbGci...r0uIGqEHTVlj3qIflUAlqwLKahqlHcCjgHsbMM5Ts",
                "exp": "1538454261"
            },
            "refreshToken": {
                "value": "c20385f75297cc22677e8dc3...48ef239bab2497ccab58c55d4bd81a72ccdef5df6",
                "exp": "1570009387"
            }
        }
    }

    
