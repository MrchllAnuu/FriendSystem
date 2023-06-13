<?php

namespace Friend\SandhyR;

use pocketmine\player\Player;
use pocketmine\Server;
use Vecnavium\FormsUI\CustomForm;
use Vecnavium\FormsUI\ModalForm;
use Vecnavium\FormsUI\SimpleForm;

class FormManager{

    private $player;
    private $playerlist = [];
    private $request = [];
    private $player2;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->friendform($player);
    }

    public function friendform(Player $player)
    {
        $manager = new FriendManager();
        $friendcount = count($manager->getArrayFriend($player));
        $form = new SimpleForm(function(Player $player, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->requestfriendform($player);
                    break;
                case 1:
                $manager = new FriendManager();
                if(is_array($manager->getArrayRequest($player))){
                    $this->friendrequestform($player);
                }
                else {
                    $player->sendMessage("You dont have friend request");
                }
                    break;
                case 2:
                $manager = new FriendManager();
                if(is_array($manager->getArrayFriend($player))){
                    $this->friendlistform($player);
                } else {
                    $player->sendMessage("You dont have friend :(");
                }

                    break;
            }
            return false;
        });
        $form->setTitle("Friend");
        $form->addButton("§2Request Friend\n§7Click To Enter");
        $form->addButton("§2Friend Request\n§7Click To Enter");
        $form->addButton("§2Friend List (" . $friendcount . ")\n§7Click To Enter");
        $form->sendToPlayer($player);
        return $form;
    }

    public function requestfriendform(Player $player){
        $list = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if ($player->getName() !== $p->getName())
                $list[] = $p->getName();
        }
        $this->playerlist[$player->getName()] = $list;
        $form = new CustomForm(function(Player $player, array $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            $manager = new FriendManager();
            $index = $data["player"];
            $playername = $this->playerlist[$player->getName()][$index];
            $friend = Server::getInstance()->getPlayerExact($playername);
            if($friend->isOnline()) {
                $manager->friendrequest($player, $friend);
            }
            $player->sendMessage("Send invite to $playername");
            return false;
        });
        $form->setTitle("Request Friend");
        $form->addLabel("Send Friend Request");
        $form->addDropdown("Select player", $this->playerlist[$player->getName()], null, "player");
        $form->sendToPlayer($player);
        return $form;
    }

    public function friendrequestform(Player $player){
        $manager = new FriendManager();
        $form = new SimpleForm(function(Player $player, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            $this->player2[$player->getName()] = $this->request[$result];
            $this->accfriend($player);
            return false;
        });
        $form->setTitle("Friend Request");
        foreach($manager->getArrayRequest($player) as $p){
            array_push($this->request, $p);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function accfriend(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            $manager = new FriendManager();
            switch ($result){
                case 0:
                    $manager->accrequest($player, $this->player2[$player->getName()]);
                    unset($this->player2[$player->getName()]);
                    break;
                case 1:
                    $manager->denyrequest($player, $this->player2[$player->getName()]);
                    unset($this->player2[$player->getName()]);
                    break;
            }
            return false;
        });
        $form->setTitle("Friend Request");
        $form->addButton("§2Accept\n§7Click To Enter");
        $form->addButton("§2Deny\n§7Click To Enter");
        $form->sendToPlayer($player);
        return $form;
    }

    public function friendlistform(Player $player){
        $manager = new FriendManager();
        $form = new SimpleForm(function(Player $player, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            $this->player2[$player->getName()] = $this->request[$result];
            $this->unfriendform($player);
            return false;
        });
        $form->setTitle("Friend Request");
        foreach($manager->getArrayFriend($player) as $p){
            array_push($this->request, $p);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function unfriendform(Player $player){
        $form = new ModalForm(function(Player $player, int $data = null){
            $result = $data;
            if ($result === null) {
                return true;
            }
            $manager = new FriendManager();
            switch ($result){
                case 0:
                    $manager->unfriend($player, $this->player2[$player->getName()]);
                    break;
                case 1:
                    $this->friendlistform($player);
                    break;
            }
            return false;
        });
        $form->setTitle("Unfriend");
        $form->setContent("Are you sure?");
        $form->addButton1("§2Yes\n§7Click To Enter");
        $form->addButton2("§2Back\n§7Click To Enter");
        $form->sendToPlayer($player);
        return $form;
    }
}
