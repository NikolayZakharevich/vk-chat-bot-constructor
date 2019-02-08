<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 07.02.19
 * Time: 10:40
 */

require_once "engine/console.php";
require_once "engine/Parser.php";
require_once "vendor/autoload.php";

use gossi\codegen\model\PhpConstant;
use gossi\codegen\generator\CodeGenerator;

define('FILE_COPY_ERROR', 'Unable to copy file ');
define('DIRECTORY_CREATE_ERROR', 'Unable to create directory ');

$parser = new Parser(__DIR__ . "/scenario.json");

constructor_createBotFiles();
constructor_createBotConfig();
constructor_uploadBotScenario();
echo "Complete";

function constructor_uploadBotScenario()
{
    echo "Uploading scenario to redis\n";

    global $parser;

    $bot_base_directory = $parser->getBotDirectory();

    echo shell_exec("cd $bot_base_directory && composer update");

    require_once "$bot_base_directory/www/config.php";
    require_once "$bot_base_directory/vendor/predis/predis/autoload.php";
    require_once "$bot_base_directory/www/engine/scenario.php";

    scenario_load($parser->get_states());
}

function constructor_createBotConfig()
{
    echo "Creating config.php\n";

    global $parser;

    $constants = array(
        new PhpConstant('BOT_BASE_DIRECTORY', $parser->getBotDirectory()),
        new PhpConstant('BOT_LOGS_DIRECTORY', $parser->getBotDirectory() . '/logs'),
        new PhpConstant('CALLBACK_API_CONFIRMATION_TOKEN', $parser->getConfirmationToken()),
        new PhpConstant('VK_API_ACCESS_TOKEN', $parser->getAccessToken()),
        new PhpConstant('REDIS_HOSTNAME', $parser->getRedisHostname()),
        new PhpConstant('REDIS_PORT', $parser->getRedisPort()),
        new PhpConstant('GREETING_MESSAGE', $parser->getGreetingMessage()),
        new PhpConstant('DEFAULT_MESSAGE', $parser->getDefaultMessage()),
    );
    $generator = new CodeGenerator(array(
        'generateEmptyDocblock' => false
    ));

    $config_name = $parser->getBotDirectory() . "/www/config.php";
    $config_body = "<?php\n\n";

    foreach ($constants as $constant) {
        $config_body = $config_body . $generator->generate($constant);
    }
    if ($parser->hasClientSecret()) {
        $config_body = $config_body . "define('CLIENT_SECRET', '" . $parser->getClientSecret() . "');\n";
    }
    if ($parser->hasReturnButton()) {
        $config_body = $config_body . "define('RETURN_BUTTON', '" . $parser->getReturnButton() . "');";
    }

    file_put_contents($config_name, $config_body);
}

function constructor_createBotFiles()
{
    echo "Creating bot files\n";

    global $parser;

    $bot_directory = $parser->getBotDirectory();
    $directory_paths = array(
        "$bot_directory",
        "$bot_directory/logs",
        "$bot_directory/static",
        "$bot_directory/www",
        "$bot_directory/www/api",
        "$bot_directory/www/engine"
    );

    $files_paths = array(
        "files/index.php" => "$bot_directory/www/index.php",
        "files/logging.php" => "$bot_directory/www/logging.php",
        "files/vk_api.php" => "$bot_directory/www/api/vk_api.php",
        "files/bot.php" => "$bot_directory/www/engine/bot.php",
        "files/Keyboard.php" => "$bot_directory/www/engine/Keyboard.php",
        "files/parser.php" => "$bot_directory/www/engine/parser.php",
        "files/scenario.php" => "$bot_directory/www/engine/scenario.php",
        "files/scenario.json" => "$bot_directory/www/scenario.json",
        "files/Storage.php" => "$bot_directory/www/engine/Storage.php",
        "files/composer.json" => "$bot_directory/composer.json"
    );


    foreach ($directory_paths as $path) {
        _constructor_createDirectory($path);
    }

    foreach ($files_paths as $src => $dest) {
        _constructor_copyFile($src, $dest);
    }

}

function _constructor_createDirectory($path)
{
    if (is_dir($path)) {
        console_info("Directory $path already exists");
        return;
    }
    if (is_file($path) || !mkdir($path)) {
        exit(DIRECTORY_CREATE_ERROR . $path);
    }
}

function _constructor_copyFile($src, $dest)
{
    if (is_file($dest)) {
        console_info("File $dest already exists");
    }

    if (!copy($src, $dest)) {
        exit(FILE_COPY_ERROR . $src);
    }
}
