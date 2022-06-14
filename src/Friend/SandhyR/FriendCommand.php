<?php

namespace Friend\SandhyR;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FriendCommand extends Command{

    public function __construct(string $name, string $description)
    {
        parent::__construct($name, $description);
        parent::setAliases(["f", "friends"]);
        $this->setPermission("friend.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
       if($sender instanceof Player){
           new FormManager($sender);
       } else {
           $sender->sendMessage("You not a player");
       }
    }
}
