<?php

declare(strict_types=1);

namespace dktapps\Scripter;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use Webmozart\PathUtil\Path;
use function copy;
use function count;
use function implode;
use function ob_end_flush;
use function ob_get_clean;
use function ob_start;
use function sys_get_temp_dir;
use function tempnam;
use function trim;
use function unlink;

class Main extends PluginBase{

	private int $scriptCounter = 0;

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($command->getName() === "script"){
			if(count($args) !== 1){
				return false;
			}
			$realPath = Path::join($this->getDataFolder(), $args[0]);
			$path = tempnam(sys_get_temp_dir(), "pmscript") . "." . $this->scriptCounter++;
			//opcache may cache this script after the first run, so we need to copy it to a tmpfile to make sure PHP
			//runs the newest version of the code
			if(!@copy($realPath, $path)){
				$sender->sendMessage(TextFormat::RED . "Script doesn't exist or permission denied");
				return true;
			}
			try{
				ob_start();
				include $path;
				$output = ob_get_clean();
				if($output !== false && trim($output) !== ""){
					$sender->sendMessage(TextFormat::GREEN . "--- Script $realPath output ---");
					$sender->sendMessage($output);
					$sender->sendMessage(TextFormat::GREEN . "--- End of script output ---");
				}else{
					$sender->sendMessage(TextFormat::GOLD . "Script $realPath ran successfully, but did not output anything!");
				}
			}catch(\Throwable $e){
				ob_end_flush();
				$sender->sendMessage(TextFormat::RED . "An error occurred while executing the script $realPath: " . implode("\n" . TextFormat::RED, Utils::printableExceptionInfo($e)));
				return true;
			}finally{
				@unlink($path);
			}
			return true;
		}
		return false;
	}
}
