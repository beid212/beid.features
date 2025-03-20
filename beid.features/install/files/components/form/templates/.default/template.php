<?
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\UI\Extension::load("ui.notification");
$features = $arResult['FEATURES'];
?>
<script>
    BX.message(<?=\Bitrix\Main\Web\Json::encode(Loc::loadLanguageFile(__FILE__))?>)
</script>
<div class="beid-form-bg" id="beid-form-modal">
    <div class="beid-form-content">
        <div class = 'beid-form-header'>
            <h3><?=Loc::getMessage('FEATURES')?></h3>
        </div>
        <div class = 'beid-form-fields'>
            <?foreach($features as $feature):?>
                <div class = 'beid-form-features-block'>
                    <div class = 'beid-form-features-input-block'>
                        <div class = 'beid-form-features-name'><?=$feature['NAME']?>:</div>
                        <input type="text" class = 'beid-form-features-input beid-form-main-input' data-is-created='<?=$feature['IS_CREATED']?>' name='<?=$feature['NAME']?>' value='<?=$feature['VALUE']?>'>
                    </div>

                    <? $featureShow = in_array($feature['NAME'],['title','description'])?'d-none':'';?>
                    <button class = 'beid-form-delete-feature <?=$featureShow?>'></button>
                </div>
            <?endforeach;?>
            <input type="hidden" value='<?=$arParams['URL']?>' name='features-url'>
        </div>
        <div class = 'beid-form-footer'>
            <div class = 'beid-add-feature-switch'><?=Loc::getMessage('ADD_FEATURE')?><span></span></div>
            <div class = 'beid-add-feature'>
                    <input type="text" class ='beid-form-features-input'>
                    <button class='beid-btn-success' id='add-new-features-btn'><?=Loc::getMessage('ADD')?></button>
            </div>


            <button class='beid-btn-success' id ='save-features-btn'><?=Loc::getMessage('SAVE')?></button>
        </div>
    </div>
</div>
