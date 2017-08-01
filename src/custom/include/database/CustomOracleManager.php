<?php

// change sugar configuration to 'db_manager' => 'CustomOracleManager'

require_once 'include/database/OracleManager.php';
require_once 'custom/include/encryption/EnvCrypt.php';

class CustomOracleManager extends OracleManager {

    public function connect(array $configOptions = null, $dieOnError = false)
    {
        global $sugar_config;

        if (is_null($configOptions)) {
            $configOptions = $sugar_config['dbconfig'];
        }

        if($configOptions['use_encryption']){
            $configOptions['db_password'] = EnvCrypt::decrypt($configOptions['db_password']);
        } else {
            $encodedPassword = EnvCrypt::encrypt($configOptions['db_password']);

            $GLOBALS['log']->security("
                    ********** IMPORTANT ENCRYPTION INSTRUCTIONS - START **********

                    Your database password is currently NOT encrypted. Please follow the two steps below to set up the encrypted password:

                    STEP 1: Change value of variable \$sugar_config['dbconfig']['db_password'] = '".$encodedPassword."'; on config.php
                    STEP 2: Append \$sugar_config['dbconfig']['use_encryption'] = true; to file config_override.php

                    ********** IMPORTANT ENCRYPTION INSTRUCTIONS - END **********");
        }

        return parent::connect($configOptions, $dieOnError);
    }

}
