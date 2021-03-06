<?php
namespace DarkWav\SAC;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\command\ConsoleCommandSender;
use DarkWav\SAC\Observer;

class SACTick extends PluginTask
{

  public function __construct($plugin)
  {
    parent::__construct($plugin);
    $this->plugin = $plugin;
  }

  public function onRun($currentTick)
  {
    foreach($this->plugin->PlayersToKick as $key=>$obs)
    {
      $obs->PlayerBanCounter++;
      if ($obs->PlayerBanCounter > 0 and $obs->PlayerBanCounter == $this->plugin->getConfig()->get("Max-Hacking-Times"))
      {
        foreach($this->plugin->getConfig()->get("MaxHackingExceededCommands") as $command)
        {
          $send = $obs->ScanMessage($command);
          $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $send);
          if($this->plugin->getConfig()->get("BanPlayerMessageBool"))
          {
            $bmsg = $this->plugin->getConfig()->get("BanPlayerMessage");
            $sbmsg = $obs->ScanMessage($bmsg);
            $this->plugin->getServer()->broadcastMessage(TextFormat::BLUE . $sbmsg);
          }
        }
        $obs->PlayerBanCounter = 0;
      }
      if ($obs->Player != null && $obs->Player->isOnline())
      {
        $obs->Player->kick(TextFormat::BLUE . $obs->KickMessage);
        if($this->plugin->getConfig()->get("KickPlayerMessageBool"))
        {
          $msg = $this->plugin->getConfig()->get("KickPlayerMessage");
          $smsg = $obs->ScanMessage($msg);
          $this->plugin->getServer()->broadcastMessage(TextFormat::BLUE . $smsg);
        }
      }   
      unset ($this->plugin->PlayersToKick[$key]);
    }  
  }
  
}