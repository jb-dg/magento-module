<?php
namespace Origines\CustomNewsletters\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Newsletter\Model\Subscriber as ModelSubscriber;
use Magento\Store\Model\ScopeInterface;

class Subscriber
{
    const ENABLE_UNSUBSCRIPTION_EMAIL = 'newsletter/subscription/enable_unsubscription_email';
    const ENABLE_CONFIRMATION_SUCCESS_EMAIL = 'newsletter/subscription/enable_confirmation_success_email';

    protected $request;
    protected $_scopeConfig;

    public function __construct(
        Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function aroundSubscribe($subject, \Closure $proceed, $email)
    {
        if ($this->request->isPost() && $this->request->getPost('subscriber_firstname')) { 

            $subscriberfirstname = $this->request->getPost('subscriber_firstname');
            $subscriberlastname = $this->request->getPost('subscriber_lastname');
            $subscriberGender = $this->request->getPost('subscriber_gender');

            $subject->setSubscriberFirstname($subscriberfirstname);
            $subject->setSubscriberLastname($subscriberlastname);
            $subject->setSubscriberGender($subscriberGender);
            $result = $proceed($email);

            try {
                $subject->save();
            }catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $result;
    }

    public function beforeSendUnsubscriptionEmail(ModelSubscriber $subject)
    {
        if ($this->_scopeConfig->getValue(self::ENABLE_UNSUBSCRIPTION_EMAIL, ScopeInterface::SCOPE_STORE)) {
            $subject->setImportMode(true);
        }
    }

    public function beforeSendConfirmationSuccessEmail(ModelSubscriber $subject)
    {
        if ($this->_scopeConfig->getValue(self::ENABLE_CONFIRMATION_SUCCESS_EMAIL, ScopeInterface::SCOPE_STORE)) {
            $subject->setImportMode(true);
        }
    }

    public function beforeSendConfirmationRequestEmail()
    {
        if ($this->_scopeConfig->getValue(self::ENABLE_CONFIRMATION_SUCCESS_EMAIL, ScopeInterface::SCOPE_STORE)) {
            $subject->setImportMode(true);
        }
    }
}
