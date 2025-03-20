<? 

namespace Beid\Features\Tables;

use Bitrix\Main\Entity;

class FeaturesTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'beid_features';
    }

    public static function getMap()
    {
        return [
            (new Entity\IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new Entity\StringField('URL'))
                ->configureRequired(),
            (new Entity\StringField('FEATURE_TYPE'))
                ->configureRequired(),
            (new Entity\StringField('VALUE'))
                ->configureRequired(),
        ];
    }
}