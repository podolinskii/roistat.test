<?php

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;

include_once __DIR__ . '/bootstrap.php';


$accessToken = getToken();
$apiClient->setAccessToken($accessToken)
    ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
    ->onAccessTokenRefresh(
        function (AccessTokenInterface $accessToken, string $baseDomain) {
            saveToken(
                [
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $baseDomain,
                ]
            );
        }
    );

//Подключение сервиса сделок
$leadsService = $apiClient->leads();

//Принимаем данные с формы
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$price = $_POST['price'];

//Проверяем фильтром существует ли контакт (по номеру)
$filter = new ContactsFilter();
$filter->setQuery($phone);

//Получаем сделки по фильтру
try {
    $contacts = $apiClient->contacts()->get($filter);
    $contact = $contacts['0'];

    //Получим коллекцию полей контакта
    $customFields = $contact->getCustomFieldsValues();

    //Если телефон передан - проверяем наличие у контакта
    if (isset($phone)) {
        //Получаем значение поля по его коду
        $phoneField = $customFields->getBy('fieldCode', 'PHONE');

        //Если поля нет - создаем и добавляем в модель
        if (empty($phoneField)) {
            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');

            //Установим значение поля,передав методу новую коллекцию с новой моделью (value)
            $phoneField->setValues(
                (new MultitextCustomFieldValueCollection())->add(
                    (new MultitextCustomFieldValueModel())->setEnum('WORK')->setValue($phone)
                )
            );
            //Добавляем поле в коллекцию
            $customFields->add($phoneField);
        }

    }
    //Проверяем аналогично email
    if (isset($email)) {

        //Получим значение поля по его коду
        $emailField = $customFields->getBy('fieldCode', 'EMAIL');
        //Если поля нет - добавляем в модель
        if (empty($emailField)) {

            //Устанавливаем код поля
            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');

            //Установим значение поля
            $emailField->setValues(
                (new MultitextCustomFieldValueCollection())->add(
                    (new MultitextCustomFieldValueModel())->setEnum('WORK')->setValue($email)
                )
            );

            //Добавляем поле в коллекцию
            $customFields->add($emailField);

        }

    }

    //Передаем контакту кастомные поля
    $contact->setCustomFieldsValues($customFields);

//    UPDATE контакта
    try {
        $apiClient->contacts()->updateOne($contact);
    } catch (AmoCRMApiException $e) {
        printError($e);
        die;
    }

    //Создаем сделку и привязываем контакт
    $lead = new LeadModel();
    $lead->setName('Новая сделка с сущ.клиентом')
        ->setPrice($price)
        ->setContacts(
            (new ContactsCollection())
                ->add($contact)

        );

     $leadsCollection = new LeadsCollection();
     $leadsCollection->add($lead);


    try {
        $leadsCollection = $leadsService->add($leadsCollection);
        echo '200';
    } catch (AmoCRMApiException $e) {
        printError($e);
        die;
    }


// ЕСЛИ КОНТАКТА ЕЩЁ НЕТ
} catch (AmoCRMApiException $e) {

    //Если нет контакта - создаём новый
    $contact = new ContactModel();
    $contact->setName($name);

    //Создаем коллекцию кастомных полей
    $customFieldsValue = new CustomFieldsValuesCollection();

    //Создадим модель кастомного поля контакта! и добавим в них код
    $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
    $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');

    //добавляем в модель коллекцию значений
    $phoneField->setValues(
    //формируем коллекцию значений
        (new MultitextCustomFieldValueCollection())
            ->add(
            //добавляем модель значений в коллекцию
                (new MultitextCustomFieldValueModel())
                    ->setEnum('WORK')
                    ->setValue($phone)
            )
    );
    //Повторяем для Email
    $emailField->setValues(
        (new MultitextCustomFieldValueCollection())
            ->add(
                (new MultitextCustomFieldValueModel())
                    ->setEnum('WORK')
                    ->setValue($email)
            )
    );

    //Добавляем в коллекцию кастомных полей новое поле
    $customFieldsValue->add($phoneField);
    $customFieldsValue->add($emailField);

    //добавляем в контакт набор кастомных полей
    $contact->setCustomFieldsValues($customFieldsValue);


    try {
        $contactModel = $apiClient->contacts()->addOne($contact);
    } catch (AmoCRMApiException $e) {
        printError($e);
        die;
    }

    $lead = new LeadModel();
    $lead->setName('Новая сделка')
        ->setPrice($price)
        ->setContacts(
            (new ContactsCollection())
                ->add($contact)

        );
    $leadsCollection = new LeadsCollection();
    $leadsCollection->add($lead);


    try {
        $leadsCollection = $leadsService->add($leadsCollection);
        echo '200';
    } catch (AmoCRMApiException $e) {
        printError($e);
        die;
    }


}







