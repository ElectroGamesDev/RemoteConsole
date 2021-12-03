<?php

namespace Electro\RemoteConsole;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\CustomFormResponse;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;

class RemoteConsole extends PluginBase implements Listener{

    public function onEnable() : void
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
                    $sender->sendForm($this->consoleForm());
                }
                else{
                    $sender->sendMessage("§cYou must be in-game to use this command!");
                }
        }
        return true;
    }

    private function consoleForm() : CustomForm{
        return new CustomForm(
            "§lRemote Console",
            [
                new Input("command", '§rEnter Command (Do not use "/")', "op Steve"),
            ],
            function(Player $submitter, CustomFormResponse $response) : void{
                $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), $response);
                $submitter->sendMessage("§aCommand has been executed as console!");
            },
        );
    }
}
