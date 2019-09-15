<?php
namespace BlackTeam\LuckyBlock\utils;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TX;
use BlackTeam\LuckyBlock\Main as MN;
#Events
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
#Other
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\command\ConsoleCommandSender;
class Events implements Listener
{
    public $eco = null;
    public $caller = null;
    public $cnf = null;
    public function __construct(MN $caller){
    	$this->caller = $caller;
    }

    public function onBreak(BlockBreakEvent $event){
    	$block = $event->getBlock();
    	$player = $event->getPlayer();
    	$cnf = $this->caller->config;
	#..... yeah i forget it, How idk
	if($event->isCancelled()) return;
    	if($block->getId() == $this->caller->config->get('LuckyBlockId')){
    		#21 possibility
    		$nbchance = mt_rand(0, 20);
    		$obj = $cnf->get("Chance-".$nbchance);
    		if(!isset($obj['Type'])){
    			$player->sendPopup($this->caller->prefix."Anything winned.");
    			$event->setDrops([Item::get(0,0,0)]);		
    		}
			if($obj['Type'] == "items"){
    			$item = $obj['idItems'];
    			$amount = $obj['amountItems'];
    			$event->setDrops([Item::get($item,0,$amount)]);
    			$player->sendPopup("You winned Item!!");
    			return;
    		}elseif(strtolower($obj['Type']) == "blocks"){
    			$theblock = $obj['idBlocks'];
    			$block->getLevel()->setBlock($block/*->asPosition()->asVector3()*/,Block::get($theblock), true);
    			$event->setCancelled();
    			$event->setDrops([Item::get(0,0,0)]);
    			return;
    		}elseif(strtolower($obj['Type']) == "money"){
    			if($this->caller->mode_eco){
    				$money = $obj["moneyToAdd"];
    				$this->caller->EconomyAPI->addMoney($player,$money);
    				$player->sendMessage($this->caller->prefix."Vous avez gagné ".$money." money! §aFélicitation!§a");
    				$event->setDrops([Item::get(0,0,0)]);
    				return;
    			}else{
    				MN::$logger->warning('Usage of The type money in the case '.$nbchance.' but economy is disabled..');
    				$player->sendMessage($this->caller->prefix."Oups .. Une erreur est survenue .. Aucun gain trouvé");
    				$event->setDrops([Item::get(0,0,0)]);
    				return;
    				}
    		}elseif(strtolower($obj['Type']) ==  "commands"){
    			$event->setDrops([Item::get(0,0,0)]);
    			$cmd = $obj['command'];
    			$cmd = str_replace(":nameofplayer:", $player->getName(), $cmd);
    			if ($obj['executor'] == "player") {
    				$this->caller->getServer()->dispatchCommand($player, $cmd);
    				$player->sendPopup($this->caller->prefix."commande en cours d'exécution..");
    			}elseif ($obj['executor'] ==  "console") {
    				$this->caller->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
    				$player->sendPopup($this->caller->prefix."commande en cours d'exécution..");
    			}else{
    				MN::$logger->warning('Usage of The type command in the case '.$nbchance.' but the executor is not player or command it\'s '.$obj['executor']);
    				$player->sendMessage($this->caller->prefix."Oups .. une erreur s'est produite .. Aucun gain trouvé pour les commandes");
    			}
    			return;
    		}elseif(strtolower($obj['Type']) =="enchant"){
                if($this->caller->mode_enc && isset($obj['idItems'],$obj['amountItems'], $obj['enchantName'], $obj['enchantLevel'])){
                    $item = $obj['idItems'];
                    $amount = $obj['amountItems'];
                    $item = Item::get($item,0,$amount);
                    $enc = $obj['enchantName'];
                    $encl = $obj['enchantLevel'];
                    $this->caller->piggy->addEnchantment($item, $enc, $encl);
                    $event->setDrops([$item]);
                    $player->sendPopup($this->caller->prefix."Vous obtenez un objet enchanté");
                }else{
                    MN::$logger->warning('Usage of The type enchant in the case '.$nbchance.' but one of them is empty OR Piggy is not available');
                    $player->sendMessage($this->caller->prefix."Oups .. une erreur s'est produite .. Aucun gain trouvé pour Enchant");
                }
            }else{
    			$player->sendPopup($this->caller->prefix."Quelque chose a gagné.");
    			$event->setDrops([Item::get(0,0,0)]);
    		}
    	}else return;
    }
}
