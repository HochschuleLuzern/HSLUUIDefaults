<?php declare(strict_types=1);
/**
 * Copyright (c) 2017 Hochschule Luzern
 *
 * This file is part of the SEB-Plugin for ILIAS.
 
 * SEB-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 
 * SEB-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with SEB-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 *
 * The SEB-Plugin for ILIAS is a refactoring of a previous Plugin by Stefan
 * Schneider that can be found on Github
 * <https://github.com/hrz-unimr/Ilias.SEBPlugin>
 */

use ILIAS\UI\Component\Image\Image;
use ILIAS\GlobalScreen\Scope\Layout\Factory\LogoModification;
use ILIAS\GlobalScreen\Scope\Layout\Provider\AbstractModificationPluginProvider;
use ILIAS\GlobalScreen\ScreenContext\Stack\CalledContexts;
use ILIAS\GlobalScreen\ScreenContext\Stack\ContextCollection;

class ilHSLUUIDefaultsGlobalScreenModificationProvider extends AbstractModificationPluginProvider
{   
    public function isInterestedInContexts() : ContextCollection
    {
        return $this->context_collection->main();
    }
    
    public function getLogoModification(CalledContexts $screen_context_stack) : ?LogoModification { 
        return $this->dic->globalScreen()->layout()->factory()->logo()->withModification(
            function (Image $current = Null) : ?Image {
                $new_type = $this->dic->http()->request()->getQueryParams()['new_type'];
                
                if ($new_type === 'grp' && $this->dic->ctrl()->getCmd() === 'create' ) {
                    $this->dic->ui()->mainTemplate()->addJavaScript($this->plugin->getDirectory().'/templates/default/additionalJSHSLU.js');
                }
                return $current;
            });
    }
}