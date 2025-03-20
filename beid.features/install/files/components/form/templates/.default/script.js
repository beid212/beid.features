
const formButton = 'bx_topmenu_btn_beid_features_button_customization_menu';

let changedFields = [];
let newFields = [];
function removeField(removeFeature)
{
    let index = changedFields.indexOf(removeFeature);
    changedFields.splice(index, 1);
    index = newFields.indexOf(removeFeature);
    newFields.splice(index, 1);
}

BX.ready(function(){
    console.log(BX.message('FEATURES'))
    $(`#${formButton}`).on('click',function(){
        $('#beid-form-modal').show();
    });

    $('#beid-form-modal').on('click', function() {
        $('#beid-form-modal').hide();
    });

    $('#beid-form-modal .beid-form-content').on('click', function(event) {
        event.stopPropagation();
    });

    $(document).on('change','input.beid-form-main-input',function() {
        if(!changedFields.includes($(this).attr('name')))
            changedFields.push($(this).attr('name'));
    });

    $('.beid-form-fields').on('click','button.beid-form-delete-feature', function(){
        let removeFeature = $(this).closest('.beid-form-features-block').find('input').attr('name');

        if(newFields.includes(removeFeature))
        {
            $(this).closest('.beid-form-features-block').remove();
            removeField(removeFeature);
            return;
        }

        BX.ajax.runComponentAction('beid.features:form', 'deleteFeature',{
            mode: 'ajax',
            data: {
                feature:removeFeature,
                url:$('input[name="features-url"]').val()
            },
        }).then((response) =>{
            BX.UI.Notification.Center.notify({
                content: BX.message('FEATURE_DELETED')
            });
            $(this).closest('.beid-form-features-block').remove();
            removeField(removeFeature);

        }, function(){
            BX.UI.Notification.Center.notify({
                content: BX.message('SERVER_ERROR')
            });
        });
    });

    $('#add-new-features-btn').on('click', function(){
            
        let newFeature = $(this).parent().find('input').val().trim();
        if(newFeature==='')  
            return;
        let english = /^[A-Za-z_]*$/;
        if (!english.test(newFeature))
        {
            BX.UI.Notification.Center.notify({
                content: BX.message('FEATURE_SHOULD_ENGLISH')
            });
            return;
        }
        if($(`input.beid-form-main-input[name=${newFeature}]`).length > 0)
        {
            BX.UI.Notification.Center.notify({
                content:BX.message('FEATURE_IS_REQUIRED')
            });
            return;
        }

        let featureBlock = $($('.beid-form-features-block')[0]).clone();

        $(featureBlock).find('.beid-form-features-name').text(newFeature+':');
        $(featureBlock).find('input').attr('data-is-created', null).attr('name',newFeature).attr('value','').val(null);
        $(featureBlock).find('.beid-form-delete-feature').removeClass('d-none');
        $(featureBlock).appendTo('.beid-form-fields');
        $('.beid-add-feature').find('input').val(null);
        changedFields.push(newFeature);
        newFields.push(newFeature);
    });

    $('#save-features-btn').on('click', function(){

        let features = {};

        changedFields.forEach(function(name){
            let element = $(`input[name=${name}]`);

            let value = element.val().trim();
            if(value==='')  
                return;

            features[element.attr('name')] = {value:element.val().trim(), isCreated: Boolean(element.attr('data-is-created'))};
            if(!element.attr('data-is-created'))
                element.attr('data-is-created',true);
        })

        BX.ajax.runComponentAction('beid.features:form', 'addFeatures',{
            mode: 'ajax',
            data: {
                features:features,
                url:$('input[name="features-url"]').val()
            },
        }).then((response) =>{
            BX.UI.Notification.Center.notify({
                content:BX.message('SAVE_SUCCESS')
            });
            changedFields.length = 0
            newFields.length = 0;
        }, function(){
            BX.UI.Notification.Center.notify({
                content: BX.message('SERVER_ERROR')
            });
        });
    });
});
