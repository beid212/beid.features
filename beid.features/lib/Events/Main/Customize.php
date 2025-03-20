<? 
namespace Beid\Features\Events\Main;

use Beid\Features\PathGuide;
use \Bitrix\Main\Web\Uri;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Beid\Features\Tables\FeaturesTable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\UserGroupTable;

class Customize
{

    public static function addFeaturesManagement()
    {
        $requestUri = Context::getCurrent()->getRequest()->getRequestUri();

        if(strpos($requestUri,'/bitrix/admin/')!==false)
            return;

        $userGroups = UserGroupTable::query()->setSelect(['GROUP_CODE'=>'GROUP.STRING_ID'])
            ->where('USER_ID', CurrentUser::get()->getId())
            ->where('GROUP.STRING_ID','contentman')
            ->exec();

        if($userGroups->getSelectedRowsCount() === 0 && !CurrentUser::get()->isAdmin())
            return;


        global $APPLICATION;
        $APPLICATION->AddPanelButton(Array(
            'ID' => 'beid_features_button_customization', // определяет уникальность кнопки
            'TEXT' => Loc::getMessage('BUTTON_TITLE'),
            'TYPE'=> 'BIG',
            'MAIN_SORT' => 100, // индекс сортировки для групп кнопок
            'SORT' => 10, // сортировка внутри группы
            'ICON' => 'icon-class', // название CSS-класса с иконкой кнопки
            'SRC' => PathGuide::up()->buildPath('/assets/images/settings.png'),
            'ALT' => Loc::getMessage('BUTTON_DESCRIPTION'),
        ));
        \CJSCore::Init(["jquery"]);
        $APPLICATION->IncludeComponent('beid.features:form','.default',
            [
                'URL'=>(new Uri($requestUri))->getLocator()
            ]
        );
    }

    public static function setFeatures()
    {
        $requestUri = Context::getCurrent()->getRequest()->getRequestUri();
        if(strpos($requestUri,'/bitrix/admin/')!==false)
            return;

        $features = FeaturesTable::query()->setSelect(['*'])->where('URL', (new Uri($requestUri))->getLocator())->fetchAll();

        global $APPLICATION;
        foreach($features as $feature)
        {
            $APPLICATION->SetPageProperty($feature['FEATURE_TYPE'],$feature['VALUE']);
        }
    }
}