<?php

namespace Electro\RemoteConsole;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\Listener;

class RemoteConsole extends PluginBase implements Listener{

    private static $instance;
    public $player;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch($command->getName()){
            case "console":
                if($sender instanceof Player){
                    if ($this->getConfig()->get("password") == "ChangeMe"){
                        $sender->sendMessage("§cYou must change the password in plugin's config.yml");
                        return true;
                    }
                    if (!$sender->hasPermission("remoteconsole.password.bypass")) {
                        if (!isset($args[0])) {
                            $sender->sendMessage("§cUsage: §a/console <password>");
                            return true;
                        }
                        if ($args[0] !== $this->getConfig()->get("password")) {
                            $sender->sendMessage("§cThe password you entered is incorrect!");
                            return true;
                        }
                    }
                    $this->consoleForm($sender);
                }
                else{
                    $sender->sendMessage("§cYou must be in-game to use this command!");
                }
        }
        return true;
    }

    public function consoleForm($player)
    {
        $form = new CustomForm(function (Player $player, $data) {
//        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
//        $form = $api->createCustomForm(function (Player $player, array $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $data[0]);
            $player->sendMessage("§aCommand has been executed as console!");
        });

        $form->setTitle("§lRemote Console");
        $form->addInput('§r§lEnter Command (Do not use "/")', 'op Player123');
        $form->sendtoPlayer($player);
        return $form;
    }

}
