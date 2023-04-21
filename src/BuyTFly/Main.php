<?php

declare(strict_types = 1);

namespace BuyTFly;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\player\Player;
use jojoe77777\FormAPI\{
    CustomForm,
    SimpleForm
};
use davidglitch04\libEco\libEco;

class Main extends PluginBase {
    private $form;
    
    public function onEnable() : void {
        $this->getLogger()->info(TextFormat::DARK_GREEN . "Buytfly enabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "buytfly":
                if (!$sender instanceof Player) {
                $sender->sendMessage("§cThis command can only be used in-game.");
                return true;
                }
                if ($sender instanceof Player) {
                    $this->buyflyForm($sender);
                    return true;
                }
        }
    }
    public function buyflyForm(Player $player) {
    $form = new CustomForm(function(Player $player, $data) {
        if ($data === null) {
            return;
        }
        if ($player->hasPermission("blazinfly.command")) {
        $player->sendMessage("[§6Omni§cCraft§r] §cYou already have fly permission.");
        return;
        }
        $server = Server::getInstance();
        $hours = $data[0];
        $price = $hours * 20000; // 20k per hour of fly time
        if(class_exists(\davidglitch04\libEco\libEco::class)){
            $economy = new libEco();
            $economy->reduceMoney($player, intval($price), function(bool $success) use ($player, $server, $hours){
                if($success){
                    $command = "ranks setpermission " . $player->getName() . " blazinfly.command " . $hours . "h";
                    $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), $command);
                    $player->sendMessage("[§6Omni§cCraft§r] §aYou have successfully bought " . $hours . " hours of fly time! do/fly");
                } else {
                    $player->sendMessage("[§6Omni§cCraft§r] §cYou do not have enough money to purchase " . $hours . " hours of fly time!");
                }
            });
        }
    });
    $form->setTitle("§bSelect the no. of hours to Buyfly-Perm");
    $form->addSlider("§eNumber of hours: ", 1, 12, 1);
    for($i = 1; $i <= 12; $i++){
        $form->addLabel("§bDuration: " . $i . "h \n§aPrice: " . ($i * 20000));
    }
    $form->sendToPlayer($player);
    }
}