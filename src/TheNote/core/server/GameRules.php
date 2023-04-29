<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\server;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

use function strval;
use function is_bool;

class GameRules{

    public const RULE_COMMAND_BLOCK_OUTPUT = "commandBlockOutput";
    public const RULE_DO_DAYLIGHT_CYCLE = "doDaylightCycle";
    public const RULE_DO_ENTITY_DROPS = "doEntityDrops";
    public const RULE_DO_FIRE_TICK = "doFireTick";
    public const RULE_DO_INSOMNIA = "doInsomnia";
    public const RULE_DO_MOB_LOOT = "doMobLoot";
    public const RULE_DO_MOB_SPAWNING = "doMobSpawning";
    public const RULE_DO_TILE_DROPS = "doTileDrops";
    public const RULE_DO_IMMEDIATE_RESPAWN = "doimmediaterespawn";
    public const RULE_DO_WEATHER_CYCLE = "doWeatherCycle";
    public const RULE_DROWNING_DAMAGE = "drowningdamage";
    public const RULE_FALL_DAMAGE = "falldamage";
    public const RULE_FIRE_DAMAGE = "firedamage";
    public const RULE_KEEP_INVENTORY = "keepInventory";
    public const RULE_MAX_COMMAND_CHAIN_LENGTH = "maxCommandChainLength";
    public const RULE_MOB_GRIEFING = "mobGriefing";
    public const RULE_PVP = "pvp";
    public const RULE_SEND_COMMAND_FEEDBACK = "sendCommandFeedback";
    public const RULE_SHOW_COORDINATES = "showcoordinates";
    public const RULE_TNT_EXPLODES = "tntexplodes";
    public const RULE_NATURAL_REGENERATION = "naturalRegeneration";
    public const RULE_RANDOM_TICK_SPEED = "randomtickspeed";

    public const RULE_TYPE_BOOL = 1;
    public const RULE_TYPE_INT = 2;
    public const RULE_TYPE_FLOAT = 3;

    public $rules = [];
    public $dirtyRules = [];

    public function __construct(){
        // default bedrock edition game rules
        $this->setBool(self::RULE_COMMAND_BLOCK_OUTPUT, true);
        $this->setBool(self::RULE_DO_DAYLIGHT_CYCLE, true);
        $this->setBool(self::RULE_DO_ENTITY_DROPS, true);
        $this->setBool(self::RULE_DO_FIRE_TICK, true);
        $this->setBool(self::RULE_DO_INSOMNIA, true);
        $this->setBool(self::RULE_DO_MOB_LOOT, true);
        $this->setBool(self::RULE_DO_MOB_SPAWNING, false);
        $this->setBool(self::RULE_DO_TILE_DROPS, true);
        $this->setBool(self::RULE_DO_IMMEDIATE_RESPAWN, false);
        $this->setBool(self::RULE_DO_WEATHER_CYCLE, true);
        $this->setBool(self::RULE_DROWNING_DAMAGE, true);
        $this->setBool(self::RULE_FALL_DAMAGE, true);
        $this->setBool(self::RULE_FIRE_DAMAGE, true);
        $this->setBool(self::RULE_KEEP_INVENTORY, false);
        $this->setInt(self::RULE_MAX_COMMAND_CHAIN_LENGTH, 65536);
        $this->setBool(self::RULE_MOB_GRIEFING, true);
        $this->setBool(self::RULE_NATURAL_REGENERATION, true);
        $this->setBool(self::RULE_PVP, true);
        $this->setBool(self::RULE_SEND_COMMAND_FEEDBACK, true);
        $this->setBool(self::RULE_SHOW_COORDINATES, false);
        $this->setBool(self::RULE_TNT_EXPLODES, true);
        $this->setInt(self::RULE_RANDOM_TICK_SPEED, 3);
    }

    public function setRule(string $name, $value, int $valueType) : bool{
        if($this->checkType($value, $valueType)){
            $this->rules[$name] = $this->dirtyRules[$name] = [
                $valueType, $value
            ];
            return true;
        }
        return false;
    }

    public function setRuleWithMatching(string $name, $value) : bool{
        if($this->hasRule($name)){
            $type = $this->rules[$name][0];
            $value = $this->convertType($value, $type);

            return $this->setRule($name, $value, $type);
        }

        return false;
    }

    public function getRule(string $name, int $expectedType, $defaultValue){
        if($this->hasRule($name)){
            $rule = $this->rules[$name];

            if($this->checkType($rule[1], $expectedType)){
                return $rule[1];
            }
        }
        return $defaultValue;
    }

    public function getRuleValue(string $name){
        return isset($this->rules[$name]) ? $this->rules[$name][1] : null;
    }

    public function hasRule(string $name) : bool{
        return isset($this->rules[$name]) and isset($this->rules[$name][0]) and isset($this->rules[$name][1]);
    }

    public function checkType($input, int $wantedType) : bool{
        switch($wantedType){
            default:
                return false;
            case self::RULE_TYPE_INT:
                return is_int($input);
            case self::RULE_TYPE_FLOAT:
                return is_float($input);
            case self::RULE_TYPE_BOOL:
                return is_bool($input);
        }
    }

    public function convertType(string $input, int $wantedType){
        switch($wantedType){
            default:
                return $input;
            case self::RULE_TYPE_INT:
                return intval($input);
            case self::RULE_TYPE_FLOAT:
                return floatval($input);
            case self::RULE_TYPE_BOOL:
                return strtolower($input) === "true" ? true : false;
        }
    }

    public function toStringValue($value) : string{
        if(is_bool($value)){
            return $value ? "true" : "false";
        }
        return strval($value);
    }

    public function setBool(string $name, bool $value) : void{
        $this->setRule($name, $value, self::RULE_TYPE_BOOL);
    }

    public function getBool(string $name, bool $defaultValue = false) : bool{
        return $this->getRule($name, self::RULE_TYPE_BOOL, $defaultValue);
    }

    public function setInt(string $name, int $value) : void{
        $this->setRule($name, $value, self::RULE_TYPE_INT);
    }

    public function getInt(string $name, int $defaultValue = 0) : int{
        return $this->getRule($name, self::RULE_TYPE_INT, $defaultValue);
    }

    public function setFloat(string $name, float $value) : void{
        $this->setRule($name, $value, self::RULE_TYPE_FLOAT);
    }

    public function getFloat(string $name, float $defaultValue = 0.0) : float{
        return $this->getRule($name, self::RULE_TYPE_FLOAT, $defaultValue);
    }

    public function getRules() : array{
        return $this->rules;
    }

    public function readSaveData(CompoundTag $nbt) : void{
        foreach($nbt->getValue() as $tag){
            if($tag instanceof StringTag){
                $this->setRuleWithMatching($tag->getType(), $tag->getValue());
            }
        }

        $this->clearDirtyRules();
    }

    public function writeSaveData() : CompoundTag{
        $nbt = new CompoundTag();

        foreach($this->rules as $name => $rule){
            $nbt->setString($name, $this->toStringValue($rule[1]));
        }

        return $nbt;
    }

    public function clearDirtyRules() : void{
        $this->dirtyRules = [];
    }

    public function getDirtyRules() : array{
        return $this->dirtyRules;
    }
}
