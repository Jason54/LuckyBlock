<?php
#c'est un bon début
namespace BlackTeam\LuckyBlock; 
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TX;
use pocketmine\utils\Config;
use BlackTeam\LuckyBlock\utils\Events;
class Main extends PluginBase
{
	public static $instance;
	public static $logger = null;
	public $mode_eco = false;
	public $mode_enc = false;
	public $prefix = TX::BLUE."[".TX::AQUA."LuckyBlock".TX::BLUE."] ".TX::RESET;
	public function onLoad(){
		self::$logger = $this->getLogger();
		self::$instance =$this;
	}
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
		@mkdir($this->getDataFolder());
		if(!file_exists($this->getDataFolder()."config.yml")){
			$this->saveResource('config.yml');
		}
		$this->config = new Config($this->getDataFolder().'config.yml', Config::YAML);
		if($this->config->get("usage_of_EconomyAPI") == "true"){
			#Maybe one day add other API
			if(!$this->getServer()->getPluginManager()->getPlugin('EconomyAPI')){
				self::$logger->error('You have enabled the usage of the plugin EconomyAPI but the plugin is not found');
				$this->isEnabled = false;
				$this->getServer()->getPluginManager()->disablePlugin($this);
				#
			}else {$this->EconomyAPI= $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
				$this->mode_eco = true;
			}
		}else $this->mode_eco = false;
		if($this->config->get("usage_of_PiggyCustomEnchants") == "true"){
			#Maybe one day add other API
			if(!$this->getServer()->getPluginManager()->getPlugin('PiggyCustomEnchants')){
				self::$logger->error('You have enabled the usage of the plugin PiggyCustomEnchants but the plugin is not found');
				$this->isEnabled = false;
				$this->getServer()->getPluginManager()->disablePlugin($this);
				#
			}else {
				$this->piggy= $this->getServer()->getPluginManager()->getPlugin('PiggyCustomEnchants');
				$this->mode_enc = true;
			}
		}else $this->mode_enc = false;
	}
	public static function getInstance(){
		return self::$instance;
	}
}