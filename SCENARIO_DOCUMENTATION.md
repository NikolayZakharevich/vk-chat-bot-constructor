# Required parameters

## bot_directory
Set absolute path to your future bot base directory. Ensure that you have read and write permissions.
```json
{
  "bot_directory": "var/www/bots"
}
```
## access_token
Set your [access token](https://vk.com/dev/access_token)
```json
{
    "access_token": "7520591e054d99d764bc90aa13f8837569d39cf9796027afc9cb32db24fad2b58f9b7d720e99a6b73cce5"
}
```
## confirmation_token
Token to register your server for Callback API
```json
{
    "confirmation_token" : "0c5ca142"
}
```

## buttons
Contains array of `button` elements.
```json
{
    "buttons" : [
       {
         "1": {
             "label": "Что это",
             "text": "Клевер — это развлекательная онлайн-игра с призами.",
             "color": "green"
           }
        },
        {
          "9": {
             "label": "Проблемы с покупкой",
             "text": "Выбери подходящий раздел:",
             "ways": [
                [
                   "10",
                   "11"
                ]
             ]
           }
        }
    ]
}
```
`button` is a JSON object, which has a unique string key (id) which doesn't displaying anywhere, used only for navigation. Parameters are:
### label
Mandatory parameter. Text, displaying on button in chat
```json
{
    "1": {
       "label": "Что это"
    }
}
```
### text
Mandatory parameter. Message, which is send after pushing the button
```json
{
    "1": {
       "text": "Клевер — это развлекательная онлайн-игра с призами."
    }
}
```
### color
Optional parameter. Color of button, accepts values 'green' (default), 'red', 'blue', 'white'.
```json
{
    "1": {
       "color": "green"
    }
}
```
### ways
Optional parameter. Id-s of buttons, which will be displayed after pushing the button. Represented as 2D array, each dimension is a raw in keyboard. 
**Note: maximum amount of buttons in a raw is 4, maximum amount of raws is 10**
```json
{
    "9": {
       "ways": [
            [
               "10",
               "11"
            ]
        ]
    }
}
```

## start_buttons
Same as `button` **ways**: buttons, displayed at the beginning
```json
{
 "start_buttons": [
    [
      "1",
      "2",
      "3"
    ],
    [
      "4",
      "5"
    ],
    [
      "6"
    ],
    [
      "7"
    ],
    [
      "8"
    ]
  ]
```
# Optional parameters
## client_secret
Set if you are getting field 'secret' from vk API
```json
{
  "client_secret": "secret"
}
```
## redis_hostname
Set if you want to change default value 127.0.0.1
```json
{
  "redis_hostname": "127.0.0.1"
}
```
## redis_port
Set if you want to change default value 6379
```json
{
  "redis_port": "6379"
}
```
## default_message
Bot will send it after recieving a regular message (without pushing a button). Default value is 'Выберите пункт'
```json
{
  "default_message": "Выберите раздел"
}
```
## greeting_message
Bot will send it to start a conversation or after restart. Default value is 'Привет!'
```json
{
    "greeting_message": "Клевер!"
}
```
## return_button
Add if you want to have a return button in every message which sends **start_buttons**'s menu to user
```json
{
    "return_button": "В главное меню"
}
```
