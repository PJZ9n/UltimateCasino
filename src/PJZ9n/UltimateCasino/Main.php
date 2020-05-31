<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of UltimateCasino.
 *
 * UltimateCasino is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * UltimateCasino is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UltimateCasino. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PJZ9n\UltimateCasino;

use PJZ9n\PluginUtils2\ResourceUtils;
use PJZ9n\PluginUtils2\Update\UpdateChecker;
use PJZ9n\PluginUtils2\Update\UpdateDescription;
use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;
use const pocketmine\NAME;
use const pocketmine\VERSION;

class Main extends PluginBase
{
    /** @var BaseLang */
    private $lang;
    
    public function onEnable(): void
    {
        //config
        $this->saveDefaultConfig();
        ResourceUtils::updateConfig($this);
        //i18n
        ResourceUtils::saveLanguageFiles($this);
        $lang = $this->getConfig()->get("lang");
        if ($lang === "default") {
            $lang = $this->getServer()->getLanguage()->getLang();
        }
        $this->lang = new BaseLang((string)$lang, $this->getDataFolder() . "locale/", "jpn");
        $this->getLogger()->info($this->lang->translateString("language.selected", [$this->lang->getName()]));
        //update
        $currentVersion = $this->getDescription()->getVersion();
        $url = "https://api.github.com/repos/PJZ9n/UltimateCasino/releases/latest";
        $userAgent = NAME . " " . VERSION;
        $callback = function (?UpdateDescription $description): void {
            if ($description === null) return;
            $version = $description->getTagName();
            $url = $description->getHtmlUrl();
            $this->getLogger()->info($this->lang->translateString("update.found", [$version, $url]));
        };
        UpdateChecker::check($currentVersion, $url, $userAgent, $callback);
    }
}
