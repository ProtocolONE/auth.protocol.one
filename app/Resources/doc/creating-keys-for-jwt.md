Creating keys for JWT
=====================

You need to create SSH keys that will be used to generate access tokens.
Use the instructions for generating the keys located on [this page](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/HEAD/Resources/doc/index.md#installation). 

After generating the keys, you need to translate them into base64
    
    cat app/config/jwt/public.pem | base64 | tr -d '\n'
    cat app/config/jwt/private.pem | base64 | tr -d '\n'    

And register them in the configuration file `app/config/parameters.yml`

    env(JWT_SECRETS_FILE_SOURCE): "LS0t...0tLS0K"
    env(JWT_PUBLIC_FILE_SOURCE): "LS0t...S0tLQo="
    