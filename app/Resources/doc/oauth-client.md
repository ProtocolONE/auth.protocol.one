Registration and authentication over oAuth
==========================================

The user can register and log in using external accounts that implement the oAuth protocol.
The list of available services can be requested by `/api/v1/oauth/sources`.

##### Example of list oAuth services
Request headers

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
The value of the parameter `destination` must be encoded, for example, through 
[encodeURIComponent] (https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent) 
for the correct transmission of control characters.

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

    Host: auth.protocolone.local
    
Request URL

    http://auth.protocolone.local/oauth/connect/vkontakte?_destination=%2Fapi%2Fv1%2Foauth%2Fresult%2Fwebsocket%3Fdsn%3Dwss%3A%2F%2F127.0.0.1%3A123%2F
    
Response object (without `JSON.stringify`)

    {
        "event": 'oauthCompletedSuccessfully',
        "message": {
            "accessToken": {
                "value": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1Mzg0NTI0NjEsImV4cCI6MTUzODQ1NDI2MSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sImlkIjoiNWJhZGZkM2Y3MmE2NTA1YWQwMDA3ZDAwIn0.LMdHx_5pbe3eBnPKv5hzj-UFM5wVANaAsi5BylEhuL9mriiCUy7RxhsvXD5BhqM-2peq0_5aSIpzr-LXT0yOEd6rqI0uSwJquaKZKXr6-1bf7Bs9UQxpfXCjNch47wUv0NXIlLnYCnTd4C5767O2L_wA3i2dfU37_nlcpXnswitbWRL6v8DJnqhNnn4-HChQhDOoxgF7YGSeoooc7P6YOV2PtWdZLujivyyIt7exqBhHavI9SfyeHyMkjvfDDfXonSYBcrZZRSDmTQpSCF5cWcHcr7XhnWseqiYFTL-BCVCc_qdogxfWQLS9l3-o237Jqp9BjKQXez__QJJgxgheQ-e0NjgUK-GTSN-Qo3Dp-HXcZLltnl5QqISa7iv2UaTbvY_7tsE6pZyinyXJYxD7-7rLuIJ676gQ3zjw2w87R9UoYzjeq6EbZK8CSSOVaS1_l4-xTySbxBdR29HhbBOb_JmAC-VuZLWOkA0fHjRieQpf5xqfhn0r417sReS523oArJecyVmK1eqlavjBshsjXBOFmXCBXAEGDph2Igacls1acS9quf5wBNJBpX0S_2VpkpBLuRbaObNJeiMBZQQw5sh23oCWkw9L_CpM-uFSqpWSkPo7aqv_p2kolahpjhMesM4ymx0MnzPeVdSYnPXj9izYnRiw3YMcLyj-Fet2rIs",
                "exp": "1538454261"
            },
            "refreshToken": {
                "value": "bb77e0b0d3a23f9065a6fa3a6697bda82f9347752115fdc63127edd132e260cf5fedcf6a80540290950b6446a31904b4820f9f9f721d47a46b041449b9660359657485ee87f44f6fe796a4e2ebe7c265db27b15fa10e8900712591866a49bd4066de3f4b626e0762cb88c7740daf7308991535a794cc556753f7f9fcd2677c88",
                "exp": "1570009387"
            }
        }
    }

    
    