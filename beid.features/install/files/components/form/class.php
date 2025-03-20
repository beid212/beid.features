<? 
use Bitrix\Main\Loader;
use Beid\Features\Tables\FeaturesTable;

if(!Loader::includeModule('beid.features'))
    throw new \Exception('The module "beid.features" is not installed');
class FeaturesForm extends CBitrixComponent 
{

    public function prepareResult()
    {
        $this->arResult['FEATURES'] = [
            'TITLE'=>[
                'NAME'=>'title',
                'VALUE'=>'',
                'IS_CREATED'=>false
            ],
            'DESCRIPTION'=>[
                'NAME'=>'description',
                'VALUE'=>'',
                'IS_CREATED'=>false
            ]];


        $features = FeaturesTable::query()->setSelect(['*'])->where('URL', $this->arParams['URL'])->fetchAll();

        foreach($features as $feature)
        {
            $this->arResult['FEATURES'][strtoupper($feature['FEATURE_TYPE'])] = [
                'NAME'=>$feature['FEATURE_TYPE'],
                'VALUE'=>$feature['VALUE'],
                'IS_CREATED'=>true
            ];
        }
    }
    public function executeComponent()
    {
        $this->prepareResult();
        $this->IncludeComponentTemplate();
    }  
}