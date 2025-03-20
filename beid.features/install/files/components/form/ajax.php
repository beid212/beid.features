<? 
use Bitrix\Main\Loader;
use Beid\Features\Tables\FeaturesTable;

if(!Loader::includeModule('beid.features'))
    throw new \Exception('The module "beid.features" is not installed');

class FeaturesController extends \Bitrix\Main\Engine\Controller
{
    public function addFeaturesAction(array $features, string $url)
	{
        $createdFeaturesTypes = [];
        foreach($features as $featureType => $feature)
        {
            if(filter_var($feature['isCreated'], FILTER_VALIDATE_BOOLEAN))
                $createdFeaturesTypes[] = $featureType;
            else
            {
                $result = FeaturesTable::add(['URL' => $url,'FEATURE_TYPE' => $featureType,'VALUE' => $feature['value']]);
                if(!$result->isSuccess())
                    return null;
            }
                
        }
        if(empty($createdFeaturesTypes))
            return true;

        $createdFeatures = FeaturesTable::query()->setSelect(['ID','FEATURE_TYPE'])->where('URL',$url)->where('FEATURE_TYPE','in',$createdFeaturesTypes)->fetchAll();

        foreach($createdFeatures as $createdFeature)
        {
            $result = FeaturesTable::update($createdFeature['ID'], ['VALUE'=>$features[$createdFeature['FEATURE_TYPE']]['value']]);
            if(!$result->isSuccess())
                return null;
        }
        return true;
	}

    public function deleteFeatureAction(string $feature, string $url)
    {
        $feature = FeaturesTable::query()->setSelect(['ID'])->where('URL',$url)->where('FEATURE_TYPE',$feature)->fetch();
        if(!empty($feature))
            $result = FeaturesTable::delete($feature['ID']);
        if(!$result->isSuccess())
            return null;
        return true;
    }
}