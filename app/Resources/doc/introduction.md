Introduction
============

API
---
The authentication API is JSON-oriented, so when sending data using the POST or PUT 
method, you need to set the `Content-Type` header with the value of `application/json`. 
All transmitted and received data uses the encoding `UTF-8`.

The data returned in response is also a JSON object and contains the header 
`Content-Type: application/json`.

We use different HTTP codes to determine the success of the server response. 
For example, code 200 indicates that the operation was successful, otherwise it 
indicates an error. Each method contains its own error codes and is described in the 
corresponding section.

Tokens
------
The services of the ProtocolOne project use tokens to identify the user, so if the 
registration or authorization succeeds, the server will return the tokens and the 
expiration time of their end.

There are 2 types of tokens: access token and refresh token.

#### Access token
It is based on [JWT](https://jwt.io/) and has a short lifetime (default is 30 minutes). The token 
contains basic information about the user and has a signature to ensure authentication. 
The token is used to access API methods that require user authentication.

#### Refresh token
Has a long lifetime (default is 1 year) and is used to retrieve/update access tokens 
when their life time is over. Different applications and sites can have their own 
refresh tokens, which can be withdrawn as needed (all at once or selectively), 
for example when changing a user's password or blocking an account. In this case, 
the refresh token is considered invalid and it will not be able to get the access token 
to work in the application.

