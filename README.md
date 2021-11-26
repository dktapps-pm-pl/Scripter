# Scripter
Run PHP scripts on the fly at runtime on a PocketMine-MP server.

This is useful for runtime debugging, when you don't want to restart the server to find out what's going on.

## How to use it?

1) Write a PHP script and put it in `plugin_data/Scripter/filename_of_your_script.php`
2) Run the script using `/script filename_of_your_script.php`

## Can I edit scripts while the server is running?

Yes, but don't declare classes, functions, or constants in scripts directly. Anything declared by previous scripts will remain in memory.

Use them for pure code only. If you want to make functions, classes etc., declare them in a separate file and then use [`require_once`](https://www.php.net/manual/en/function.require-once.php) in your script.

## Why not /eval?

`eval` is too inconvenient - having to write your entire code into the console (or MC game) is just too inconvenient.

I was inspired by `mcfunction`, which allows you to put a list of complex commands in a file and run them at runtime.

## Can I use this in production?

Please don't! I take no responsibility if you get hacked by using this plugin.

## Examples
### Get the target block of the player `mctestDylan` and spawn a `RedstoneParticle` at that block
```php
<?php

declare(strict_types=1);

use pocketmine\block\VanillaBlocks;
use pocketmine\Server;
use pocketmine\world\particle\RedstoneParticle;

$player = Server::getInstance()->getPlayerExact("mctestDylan");
if($player === null){
	echo "no player\n";
	return;
}

$pos = $player->getTargetBlock(100, [VanillaBlocks::AIR()->getId() => true])?->getPosition();
if($pos === null){
	echo "no position\n";
	return;
}

$player->getWorld()->addParticle($pos, new RedstoneParticle(10));
echo "success!\n";
