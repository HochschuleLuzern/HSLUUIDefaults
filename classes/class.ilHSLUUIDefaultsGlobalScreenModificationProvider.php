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
    
    public function getLogoModification(CalledContexts $screen_context_stack) : ?LogoModification
    {
        return $this->dic->globalScreen()->layout()->factory()->logo()->withModification(
            function (Image $current = null) : ?Image {
                $new_type = $this->dic->http()->request()->getQueryParams()['new_type'];
                
                if ($new_type === 'grp' && $this->dic->ctrl()->getCmd() === 'create') {
                    $this->dic->globalScreen()->layout()->meta()->addOnloadCode('document.getElementById("didactic_type_dtpl_1").click()');
                }
                
                if ($this->dic->ctrl()->getCurrentClassPath() === ['ildashboardgui', 'ilpdselecteditemsblockgui'] &&
                    $this->dic->ctrl()->getCmd() === 'manage') {
                    $this->dic->globalScreen()->layout()->meta()->addJs($this->plugin->getDirectory() . '/templates/default/additionalJSHSLU.js');
                    $this->dic->globalScreen()->layout()->meta()->addOnloadCode('il.hslu.initializeCheckboxes()');
                }
                
                return $current;
            }
        );
    }
}
