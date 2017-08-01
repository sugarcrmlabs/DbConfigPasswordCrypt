<?php

use Sugarcrm\Sugarcrm\Security\Crypto\Blowfish;

class EnvCrypt
{
    protected static function getKey()
    {
        global $sugar_config;

        // populate default key as a file system based blowfish
        $result = Blowfish::getKey('custom_encrypt');

        if(empty($sugar_config['dbconfig']['filesystem_encryption_only'])) {
            $key = getenv('sugar_blowfish_key');
            if ($key !== false) {
                if (strlen($key) > 56 || strlen($key) < 4) {
                    $GLOBALS['log']->security("Retrieved encryption key from ENV variable 'sugar_blowfish_key'. It's  " . strlen($key)
                            . " chars long but should be between 4 and 56 chars long (32 to 448 bits). Will use the file system based one.");
                } else {
                    $result = $key;
                }
            } else {
                $GLOBALS['log']->security("Could not retrieve encryption key from ENV variable 'sugar_blowfish_key'. Will use the file system based one. To suppress this security message and leverage only file system based blowfish encryption set on config_override.php \$sugar_config['dbconfig']['filesystem_encryption_only'] = true;");
            }
        }

        return $result;
    }

    public static function encrypt($data, $key = '')
    {
        if (empty($key)) {
            $key = self::getKey();
        }

        $encrypted = Blowfish::encode($key, $data);

        return base64_encode($encrypted);
    }

    public static function decrypt($data, $key = '')
    {
        $data = base64_decode($data);

        if (empty($key)) {
            $key = self::getKey();
        }

        return trim(Blowfish::decode($key, $data));
    }
}
