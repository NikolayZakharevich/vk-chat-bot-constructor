# Vk chat bot constructor

PHP tool for VK chat bot constructing. Created bot interacts with users using chat keyboards.

## 1. Prerequisites

* Redis 5.0 or later
* PHP 7.1 or later
* Composer 1.8 or later

## 2. Installation

Clone this repository or download all files to directory `Bot_Constructor`. The bot constructor can be installed using Composer by running the following command:

```sh
cd Bot_Constructor
composer update
```

## 3. Initialization

Create your config.json file in constructor directory ([example](https://github.com/NikolayZakharevich/Bot_Constructor/blob/master/scenario.json)). Documentation can be found [here](https://github.com/NikolayZakharevich/Bot_Constructor/blob/master/SCENARIO_DOCUMENTATION.md). 

Then run following command:
```sh
php bot_constructor.php
```

## 4. Finish

Congratulation! You can find your bot in base directory, which is specified in scenario.json.
