<?php

namespace Origines\Cleaner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_CLEANER_LOG_ENABLED = 'system/cleaner/enabled';
    const XML_PATH_CLEANER_LOG_DAYS = 'system/cleaner/days';
    const DEFAULT_SAVE_DAYS = 30;

    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_CLEANER_LOG_ENABLED);
    }

    public function getCleanerDaysLogs()
    {
        $cleanerDays = (int)$this->scopeConfig->getValue(self::XML_PATH_CLEANER_LOG_DAYS);
        if (!$cleanerDays) {
            $cleanerDays = self::DEFAULT_SAVE_DAYS;
        }
        return $cleanerDays;
    }
}
