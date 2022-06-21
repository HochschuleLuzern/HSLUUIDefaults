<?php declare(strict_types = 1);

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